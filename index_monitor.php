<?php
require_once 'getConfig.php';
require_once 'jsonRPCClient.php';

$totalHashRate = 0;
$json = '';

// 3 seconds max.
set_time_limit(10);
error_reporting(0);

defined('API_HOST') || define('API_HOST', 'localhost');
defined('API_PORT') || define('API_PORT', 4048);

function getsock($host,$port,$cmd) {

    $timeout = 3;
    $socket = @stream_socket_client(
            "tcp://$host:$port",
            $errNo,
            $errorMessage,
            $timeout,
        STREAM_CLIENT_CONNECT,
            stream_context_create(array())
    );

    if ($socket === false) {
        //throw new UnexpectedValueException("Failed to connect: $errorMessage");
        $data = "error=Failed to connect: $errorMessage|";
    }
    else {
        fwrite($socket, $cmd);
        stream_set_timeout($socket, 1);
        $data = stream_get_contents($socket);
        $info = stream_get_meta_data($socket);
        fclose($socket);
        if ($info['timed_out']) {
            $data = 'error=Connection timed out|';
        }
    }
    $data = strToArray($data);
    return $data;
}


function strToArray($sockData) {

	$objs = explode('|', $sockData);
	array_pop($objs);
	$data = array();
	$items = explode(';', $objs[0]);
	foreach ($items as $item)
	{
		$id = explode('=', $item);
		$data[$id[0]] = $id[1];
		if($id[0] == 'KHS') $data['HM'] = $id[1]*60*1000; //cpuminer returns KHS so we add Hash/min for low hash devices. Need Fireworms latest to take advantage
	}
		
	return $data;
}


function getDataFromHost($host,$port)
{
	$summary=getsock($host,$port,'summary');
	if ($summary==false) return false;
    return json_encode(array('summary'=>$summary));
}


function getdataFromPeers()
{
	global $configs, $hostsFile;
    $configs= file($hostsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$data = array();
	foreach ($configs as $host) {
	    $conf = explode(':',$host);
		$data[$host] = json_decode(getDataFromHost($conf[0],(isset($conf[1])) ? $conf[1] : API_PORT), TRUE);
	}
	return $data;
}

function ignoreField($key,$solo)
{
    if($solo == TRUE){
	     $ignored = array('NAME','VER','ALGO','GPUS','SOLV','ACCMN','CPU','TS','API','GPU','CARD','FAN','REJ','DIFF','UPTIME');
    }
    else{
         $ignored = array('NAME','VER','ALGO','GPUS','ACC','ACCMN','CPU','TS','API','GPU','CARD','FAN','REJ','DIFF','UPTIME');
    }
    return in_array($key, $ignored);
}


function translateField($key)
{
	$intl = array();
	$intl['NAME'] = 'Software';
	$intl['VER'] = 'Version';
	$intl['KHS'] = 'KH/S';
	$intl['HM'] = 'H/M';

	$intl['ALGO'] = 'Algorithm';
	$intl['GPUS'] = 'GPUs';
	$intl['CPUS'] = 'Threads';
	$intl['H/m'] = 'Hash rate (H/m)';
	$intl['SOLV'] = 'Shares Accepted';
	$intl['ACC'] = 'Blocks found';
	$intl['ACCMN'] = 'Accepted / mn';
	$intl['REJ'] = 'Rejected';
	$intl['DIFF'] = 'Difficulty';
	$intl['UPTIME'] = 'Miner up time';
	$intl['TS'] = 'Last update';

	$intl['TEMP'] = 'T c';
	$intl['FAN'] = 'Fan %';
	$intl['FREQ'] = 'CPU Freq.';
	$intl['PST'] = 'P-State';

	if (isset($intl[$key]))
		return $intl[$key];
	else
		return $key;
}

function translateValue($key,$val,$data=array())
{
	switch ($key) {
		case 'UPTIME':
			$min = floor(intval($val) / 60);
			$sec = intval($val) % 60;
			$val = "${min}m ${sec}s";
			if ($min > 60) {
				$hrs = floor($min / 60);
				$min = $min % 60;
				$val = "${hrs}h ${min}m";
			}
			break;
		case 'NAME':
			$val = $data['NAME'].'�&nbsp;'.$data['VER'];
			break;
		case 'FREQ':
                        $val = sprintf("%.2fGHz", floatval($val)/1000000);
			break;
		case 'TS':
			$val = strftime("%H:%M:%S", (int) $val);
			break;
	}
	return $val;
}

function displayData($data,$config_array)
{
	global $totalHashRate;
    $htm = '';
	$totalHashRate = 0;
	foreach ($data as $name => $stats) {
		$htm .= '<table id="tb_'.$name.'" class="stats">'."\n";
		$htm .= '<tr><th class="machine" colspan="2">'.$name."</th></tr>\n";
        $summary = (array) $stats['summary'];
        foreach ($summary as $key=>$val) {
            if (!ignoreField($key,$config_array['solo'])) {
                if ($key == 'error') {
                    $htm .= '<tr><td class="val" colspan="2">' . translateValue($key, $val, $summary) . "</td></tr>\n";
                } else {
                    $htm .= '<tr><td class="key">' . translateField($key) . '</td>' .
                        '<td class="val">' . translateValue($key, $val, $summary) . "</td></tr>\n";
                    if ($key == 'KHS') {
                        $totalHashRate += $val;
                    }
                }
            }
        }
        $htm .= "</table>\n";
	}
	
    // totals for solo then pool

    if ($config_array['solo'] == TRUE) {
            $url = 'http://'.$config_array['walletuser'].':'.$config_array['walletpassword'].'@'.$config_array['walletaddress'].':33987';
            $verium = new jsonRPCClient($url);
        
            $balance = $verium->getbalance();
            $mininginfo = $verium->getmininginfo();

            $blocktime=(-13.03*log($mininginfo['difficulty'])+180)/60;
            $blocks_hr=60.0/$blocktime;
            $network_vrm=$blocks_hr*$mininginfo['blockreward (VRM)'];
            $vrm_day=$network_vrm*($totalHashRate*60)/$mininginfo['nethashrate (kH/m)']*24;
        
            $totals = '<table cellpadding="10"><tr><td>Total kH/M: '.number_format($totalHashRate*60,3).'</td>
              <td>KH/S: '.number_format($totalHashRate,4).'</td>
              <td>Network kH/M: '.number_format($mininginfo['nethashrate (kH/m)'],0).'</td>
              <td>Blocks/hr '.number_format($blocks_hr,2).'</td></tr>
           <tr><td>VRM Balance: '.number_format($balance,2).'</td>
              <td>Block: '.$mininginfo['blocks'].'</td>
              <td>Block Rewared: '.number_format($mininginfo['blockreward (VRM)'],2).'</td>
              <td>VRM/day : '.number_format($vrm_day,2).'</td></tr>
           </table>'."\n";
            
        
    } else {
        $totals = '<div class="totals"><h2>Totals:'.$ini['solo'].'<br />KH/S: '.$totalHashRate.'<br />KH/M: '.($totalHashRate*60).'</h2></div>'."\n";
	    
        
    }
    
    return $totals.$htm;
}

$data = getdataFromPeers();
?>
<?=displayData($data,$ini_array['CONFIG'])?>

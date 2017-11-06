<?php
/* cpuminer API sample UI */

$hostsFile = '/var/www/public_html/minerHosts'; // modify this path to the minerHosts file
$totalHashRate = 0;
$json = '';

// 3 seconds max.
set_time_limit(3);
error_reporting(0);

defined('API_HOST') || define('API_HOST', 'localhost');
defined('API_PORT') || define('API_PORT', 4048);

/*
Modified getsock to use fsock for better compatability. 
It's a blocking method, but it should still run pretty fast, even with many machines to check.
TODO: Will add multi-threads later.
*/

function getsock($host,$port,$cmd) {
	// Seconds to wait for a successful connection
    $connectTimeout = 5;
    // Seconds to wait to receive data
    $receiveTimeout = 5;

    $socket = @fsockopen($host, $port, $errNo, $errStr, $connectTimeout);

    if( !$socket || !stream_set_timeout( $socket, $receiveTimeout ) )
    {
        return FALSE;
    }

    fwrite( $socket, $cmd );
    $data = fread( $socket, 256 );
    fclose( $socket );
	$data = strToArray($data);
    return $data;
}


function strToArray($sockData) {

	$objs = array();
	$objs = explode('|', $sockData);
	array_pop($objs);
	$data = array();
	$items = explode(';', $objs[0]);
	foreach ($items as $item)
	{
		$id = explode('=', $item);
		$data[$id[0]] = $id[1];
		if($id[0] == 'KHS') $data['KHM'] = $id[1]*60; //cpuminer returns KHS so we add KHM as a data point for those interested
	}
		
	return $data;
}


function getDataFromHost($host)
{
	$summary=getsock($host,API_PORT,'summary');
	if ($summary==false) return false;
    return json_encode(array('summary'=>$summary));
}


function getdataFromPeers()
{
	global $configs, $hostsFile;

    $configs= file($hostsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

	$data = array();
	foreach ($configs as $name => $conf) {
		$data[$conf] = json_decode(getDataFromHost($conf), TRUE);
	}
	return $data;
}

function ignoreField($key)
{
	$ignored = array('NAME','VER','ALGO','GPUS','SOLV','ACCMN','CPU','TEMP','TS','API','GPU','CARD','FAN','REJ','DIFF','UPTIME');
	return in_array($key, $ignored);
}

function translateField($key)
{
	$intl = array();
	$intl['NAME'] = 'Software';
	$intl['VER'] = 'Version';
	$intl['KHS'] = 'KH/S';
	$intl['KHM'] = 'KH/M';

	$intl['ALGO'] = 'Algorithm';
	$intl['GPUS'] = 'GPUs';
	$intl['CPUS'] = 'Threads';
	$intl['H/m'] = 'Hash rate (H/m)';
	$intl['SOLV'] = 'Found blocks';
	$intl['ACC'] = 'Blocks found';
	$intl['ACCMN'] = 'Accepted / mn';
	$intl['REJ'] = 'Rejected';
	$intl['DIFF'] = 'Difficulty';
	$intl['UPTIME'] = 'Miner up time';
	$intl['TS'] = 'Last update';

	$intl['TEMP'] = 'T°c';
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
			$val = $data['NAME'].'&nbsp;'.$data['VER'];
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

function displayData($data)
{
	global $totalHashRate;
    $status = 'No connection';
	$noconnection = '';
    $htm = '';
    $htm1 = '';
	$totals = array();
	$totalHashRate = 0;

	foreach ($data as $name => $stats) {

		$htm .= '<table id="tb_'.$name.'" class="stats">'."\n";
		$htm .= '<tr><th class="machine" colspan="2">'.$name."</th></tr>\n";
		if ($stats == NULL) { //issue with connection or miner did not return any data
			   $htm .= '<tr><td class="key">'.$status.'</td>'.
               '<td class="val">'.$noconnection."</td></tr>\n";
        }
        else {
        	$summary = (array) $stats['summary'];    
            foreach ($summary as $key=>$val) {
                    if (!ignoreField($key))
					$htm .= '<tr><td class="key">'.translateField($key).'</td>'.
						'<td class="val">'.translateValue($key, $val, $summary)."</td></tr>\n";
                    if ($key=='KHS') {
                       $totalHashRate += $val;
                    }
            }
		}
		$htm .= "</table>\n";
	}
	// totals

        $htm1 .= '<div class="totals"><h2>Totals:<br />KH/S: '.$totalHashRate.'<br />KH/M: '.($totalHashRate*60).'</h2>'."\n";
	    //$htm .= '<li><span class="algo">'.$algo.":</span>$hashrate kH/s</li>\n";
        $htm1 .= '</div>';
        //$htm2 = $htm1.$htm;
        $htm = $htm1.$htm;
	
	//echo "Total Hash Rate: $totalHashRate";
	return $htm;
}

function displayHashTotal()
{
         global $totalHashRate;

         $htm = '';
         $htm .= ''.$totalHashRate.'';
         return $htm;
}

$data = getdataFromPeers();
$totalHash = displayHashTotal();

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<?=displayData($data)?>
</body>
</html>
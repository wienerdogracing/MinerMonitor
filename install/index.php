<?php
require_once '../getConfig.php';
define('YES', '<span class="glyphicon glyphicon-ok-sign" style="color:green;"></span>');
define('NO', '<span class="glyphicon glyphicon-remove-sign" style="color:red;" title="%s"></span>');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPage =  'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: '.$currentPage);

    die;
}

function checkReqs() {
    return array(
        array(
            'name'=>'parse_ini_file is enabled',
            'req'=>1,
            'value'=>(int)function_exists('parse_ini_file'),
            'ifno'=>'remove parse_ini_file from disabled_functions list'
        ),
        array(
            'name'=>'config.ini exists',
            'req'=>1,
            'value'=>(int)file_exists(CONFIG),
            'ifno'=>'create config.ini in base path'
        ),
        array(
            'name'=>'config.ini is writable',
            'req'=>1,
            'value'=>(int)is_writeable(CONFIG),
            'ifno'=>'Check web server has permissions to file.&#10;Permissions should be 644 or 664 on *nix systems',
        ),
    );
}
$reqs = checkReqs();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>MinerMonitor Install</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h2>MinerMonitor Install Script</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <h3>Requirements <small>Mouse over errors for more info</small></h3>
        </div>
    </div>
    <div class="row">
        <table class="table">
            <tr>
                <td>BASE PATH</td>
                <td><?=BASE_PATH;?></td>
            </tr>
            <?php
            foreach($reqs as $req){
                $YESNO = $req['req'] == $req['value'] ? true : false;
                echo '<tr>
                        <td>'.$req['name'].'</td>
                        <td>'.($YESNO ? YES : sprintf(NO,$req['ifno'])).'</td>
                      </tr>';
            }
            ?>
        </table>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <hr />
        </div>
    </div>
    <form method="post">
    <div class="row">

        <div class="col-sm-12">
            <h3>Config Options</h3>
        </div>
    </div>
    <div class="row">
        <div class="radio">
            <label><input type="radio" name="hostsfile" id="hostsfile" value="config">Use config.ini for storing hosts</label>
        </div>
    </div>
    <div class="row">
        <div class="radio">
            <label><input type="radio" name="hostsfile" id="hostsfile" value="minerHosts">Use minerHosts file for storing hosts</label><br />
            <label style="width:95%;"><input type="text" class="form-control" name="hostfileloc" id="hostfileloc" placeholder="<?=BASE_PATH?>"  style="width:95%;"></label>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <input type="submit" value="Update Config" class="btn-primary">
        </div>
    </div>
    </form>
</div>

<script>
$(function(){
    $("input[name='hostsfile']").change(function() {
        if($(this).val()==='config') $('#hostfileloc').prop({'disabled':true});
        else $('#hostfileloc').prop({'disabled':false})
    });
});
</script>
</body>
</html>

<html>
<head>
	<title>Verium Miner Monitor</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="styles.css">
<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
</head>
<body>

<script>
    function loadStats () {
        $("#page").load("index_monitor.php");
    }


    $(function() {
        var auto_refresh = setTimeout(function run() {
            loadStats();
            setTimeout(run, 30000); //default refresh is 30 seconds AFTER last loadstats is done rather than every 30 seconds
        },0); //first run is instant
    });




</script>

<div id="header">
<h1>Miner Monitor</h1>
</div>

<div id="page">Working...</div>

<div id="footer">
<p><a href="https://github.com/effectsToCause/veriumMiner">Download veriumMiner</a></p>
</div>

</body>
</html>
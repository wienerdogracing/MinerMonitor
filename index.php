<html>
<head>
	<title>Verium Miner Monitor</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="styles.css">
<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
</head>
<body>

<script>
    $(function() {
        var auto_refresh;
        auto_refresh = setTimeout(function loadStats () {
            $("#page").load("index_monitor.php", function () {setTimeout(loadStats, 30000);});
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
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
  $("#page").load("index_monitor.php");
});


var auto_refresh = setInterval(
(function () {
    $("#page").load("index_monitor.php"); //Load the content into the div
}), 5000); //Number of seconds in microseconds to refresh the stats; 5000 is 5 seconds
</script>

<div id="header">
<h1>Miner Monitor</h1>
</div>

<div id="page"></div>

<div id="footer">
<p><a href="https://github.com/effectsToCause/veriumMiner">Download veriumMiner</a></p>
</div>

</body>
</html>
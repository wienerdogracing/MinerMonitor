<?php
define('BASE_PATH',     dirname( __FILE__ ).'/');
define('CONFIG', BASE_PATH . "config.ini");

$ini_array = parse_ini_file(CONFIG, true);
$hostsFile = $ini_array['CONFIG']['minerFile'];
<?php
require_once 'getConfig.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPage =  'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: '.$currentPage);

    die;
}


/**
 * @param $file
 * @param $data
 */
function modify_hostfile($file, $data){
    $fh = fopen($file,'w+');
    // using file_put_contents() instead of fwrite()
    file_put_contents($file, $data);
    fclose($fh);
}

/**
 * @param $config_file
 * @param $section
 * @param $key
 * @param $value
 */
function config_set($config_file, $section, $key, $value) {
    $config_data = parse_ini_file($config_file, true);
    $config_data[$section][$key] = $value;
    $new_content = '';
    foreach ($config_data as $section => $section_content) {
        $section_content = array_map(function($value, $key) {
            return "$key=$value";
        }, array_values($section_content), array_keys($section_content));
        $section_content = implode("\n", $section_content);
        $new_content .= "[$section]\n$section_content\n";
    }
    file_put_contents($config_file, $new_content);
}
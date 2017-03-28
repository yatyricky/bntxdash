<?php
require_once 'config.php';
require_once 'Utils.php';
header('Access-Control-Allow-Origin: *');

$array = array();
$handle = fopen($GLOBALS['pathSystemLog'], "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        $array[] = $line;
    }
    fclose($handle);
} else {
    $array[] = "Error reading log.";
}
echo json_encode($array);

Utils::writeServerLog('Check system log');

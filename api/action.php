<?php
require_once("config.php");
header("Access-Control-Allow-Origin: *");

$do = $_POST['do'];
// $do = "view-robot-config";
$made = "";

switch ($do) {

case 'system-log':
    $array = array();
    $handle = fopen($config['pathSystemLog'], "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            $array[] = $line;
        }
        fclose($handle);
    } else {
        $array[] = "Error reading log.";
    }
    $json['resp'] = $array;
    $made = "Check system log";
break;

default:
break;
}

function sortQueryDiamonds($a, $b) {
    return strcmp($a['time'], $b['time']) * -1;
}
function getRealIpAddr(){
    if (!empty($_SERVER['HTTP_CLIENT_IP'])){   //check ip from share internet
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  //to check ip is pass from proxy
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
echo json_encode($json);

file_put_contents($config['pathSystemLog'], "[".date('Y/m/d H:i:s')."][".getRealIpAddr()."]".$made.PHP_EOL , FILE_APPEND | LOCK_EX);
?>
<?php
require_once("config.php");
header("Access-Control-Allow-Origin: *");

$do = $_POST['do'];
// $do = "robot_coins_sum";
$made = "";

switch ($do) {

case 'robot_coins_sum':
    $array = array();
    $handle = fopen($config['pathBotCoins'], "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            $tokens = explode(',', trim($line));
            if (count($tokens) == 3){
                $obj['time'] = $tokens[0];
                $obj['value'] = $tokens[1];
                $obj['played'] = $tokens[2];
                $array[] = $obj;
            }
        }
        fclose($handle);
    } else {
        $array[] = "Error reading robot sum.";
    }
    $json['resp'] = $array;
    $made = "Check system robot sum";
break;

default:
break;
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

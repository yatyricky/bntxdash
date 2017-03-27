<?php
require_once("config.php");
header("Access-Control-Allow-Origin: *");

$do = $_POST['do'];
// $do = "robot_coins_sum";
$made = "";

switch ($do) {

case 'player-win-robots':
    $made = 'http://'.$config['serverLocalOP'].':'.$config['portLocal'].'/groot/merge_player_win_robots.py';
    $params = "dateStart=".$_POST['start']."&dateEnd=".$_POST['end'];
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $made,
        CURLOPT_USERAGENT => 'robot-analysis-performance',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $params
    ));
    $resp = curl_exec($curl);
    curl_close($curl);

    $json = json_decode($resp);
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

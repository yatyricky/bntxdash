<?php
require_once 'config.php';

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

function writeServerLog($made) {
    file_put_contents($GLOBALS['pathSystemLog'], '['.date('Y/m/d H:i:s').']['.getRealIpAddr().']'.$made.PHP_EOL , FILE_APPEND | LOCK_EX);
}
?>
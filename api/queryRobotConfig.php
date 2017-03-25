<?php
require_once 'config.php';
require_once 'writeServerLog.php';
header('Access-Control-Allow-Origin: *');

$made = 'http://'.$GLOBALS['serverLocalOP'].':'.$GLOBALS['portLocal'].'/groot/view_bot_config.py';
$params = '';
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $made,
    CURLOPT_USERAGENT => 'view-robot-config',
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => $params
));
$resp = curl_exec($curl);
curl_close($curl);

echo $resp;

writeServerLog($made);
?>
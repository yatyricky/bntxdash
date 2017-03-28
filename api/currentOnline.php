<?php
require_once 'config.php';
require_once 'Utils.php';
header('Access-Control-Allow-Origin: *');

$made = 'http://'.$GLOBALS['serverProdZH'].':'.$GLOBALS['portGS'].'/system/info';

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $made,
    CURLOPT_USERAGENT => 'Concurrent Users',
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => ""
));
$resp = curl_exec($curl);
curl_close($curl);

echo explode(':', $resp)[1];

Utils::writeServerLog($made);

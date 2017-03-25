<?php
require_once 'config.php';
require_once 'writeServerLog.php';
header('Access-Control-Allow-Origin: *');

$server = $GLOBALS['serverBeta'];
if ($_POST['server'] == 'prod') {
    $server = $GLOBALS['serverProdZH'];
}
$made = 'http://'.$server.':'.$GLOBALS['portGS'].'/account/get_robot_state_info';
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $made,
    CURLOPT_USERAGENT => 'Query robot info',
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => ''
));
$resp = curl_exec($curl);
curl_close($curl);

echo $resp;

writeServerLog($made);
?>
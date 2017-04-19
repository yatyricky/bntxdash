<?php
require_once 'config.php';
require_once 'Utils.php';
header('Access-Control-Allow-Origin: *');

$accountId = intval($_POST['accountId']);
$diamonds = intval($_POST['diamonds']);
$chips = floatval($_POST['chips']);

$server = $GLOBALS['serverAlpha'];
switch ($_POST['server']) {
    case 'prod':
        $server = $GLOBALS['serverProdZH'];
        break;
    case 'proden':
        $server = $GLOBALS['serverProdEN'];
        break;
    case 'beta':
        $server = $GLOBALS['serverBeta'];
        break;
    default:
        break;
}

$made = "Mod player stats:";
$json['resp'] = "Account ID: ".$accountId;

if ($diamonds != 0) {
    $curl = curl_init();
    $reqs = 'http://'.$server.':'.$GLOBALS['portGS'].'/account/change_player_property';
    $params = "ChangeProperty=acc_id=".$accountId.",propertyId=3,propertyValue=".$diamonds;
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $reqs,
        CURLOPT_USERAGENT => 'Mod diamonds',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $params,
    ));
    $resp = curl_exec($curl);
    curl_close($curl);

    $made = $made.$reqs."/".$params;
    $json['resp'] = $json['resp']." diamonds".($diamonds > 0?"+":"").$diamonds;
}

if ($chips != 0) {
    $curl = curl_init();
    $reqs = 'http://'.$server.':'.$GLOBALS['portGS'].'/account/change_player_property';
    $params = "ChangeProperty=acc_id=".$accountId.",propertyId=1,propertyValue=".$chips;
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $reqs,
        CURLOPT_USERAGENT => 'Mod chips',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $params,
    ));
    $resp = curl_exec($curl);
    curl_close($curl);

    $made = $made.$reqs."/".$params;
    $json['resp'] = $json['resp']." chips".($chips > 0?"+":"").$chips;
}

if ($_POST['vip'] != 0) {
    $curl = curl_init();
    $reqs = 'http://'.$server.':'.$GLOBALS['portGS'].'/account/change_player_property';
    $params = "ChangeProperty=acc_id=".$accountId.",propertyId=15,propertyValue=".$_POST['vip'];
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $reqs,
        CURLOPT_USERAGENT => 'Mod vip',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $params,
    ));
    $resp = curl_exec($curl);
    curl_close($curl);

    $made = $made.$reqs."/".$params;
    $json['resp'] = $json['resp']." vip=".$_POST['vip'];
}

echo json_encode($json);

Utils::writeServerLog($made);

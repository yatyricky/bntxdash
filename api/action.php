<?php
require_once("config.php");
header("Access-Control-Allow-Origin: *");

$do = $_POST['do'];
// $do = "view-robot-config";
$made = "";

switch ($do) {
case 'mod-player-stats':
    $accountId = intval($_POST['accountId']);
    $diamonds = intval($_POST['diamonds']);
    $chips = floatval($_POST['chips']);

    $server = $config['serverAlpha'];
    switch ($_POST['server']) {
        case 'prod':
            $server = $config['serverProdZH'];
            break;
        case 'beta':
            $server = $config['serverBeta'];
            break;
        default:
            break;
    }

    $made = "Mod player stats:";
    $json['resp'] = "Account ID: ".$accountId;

    if ($diamonds != 0) {
        $curl = curl_init();
        $reqs = 'http://'.$server.':'.$config['portGS'].'/account/change_player_property';
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
        $reqs = 'http://'.$server.':'.$config['portGS'].'/account/change_player_property';
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
        $reqs = 'http://'.$server.':'.$config['portGS'].'/account/change_player_property';
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
break;

case 'get-robot-info':
    $server = $config['serverBeta'];
    if ($_POST['svr'] == "prod") {
        $server = $config['serverProdZH'];
    }
    $made = 'http://'.$server.':'.$config['portGS'].'/account/get_robot_state_info';
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $made,
        CURLOPT_USERAGENT => 'Query robot info',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => ""
    ));
    $resp = curl_exec($curl);
    curl_close($curl);

    $json = json_decode($resp);
break;

case 'concurrent-users':
    $made = 'http://'.$config['serverProdZH'].':'.$config['portGS'].'/system/info';
    
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

    $json['resp'] = $resp;
break;

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

case 'robot-analysis-retention':
    $made = 'http://'.$config['serverLocalOP'].':'.$config['portLocal'].'/groot/nu_vs_bot.py';
    $params = "date=".$_POST['value'];
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $made,
        CURLOPT_USERAGENT => 'robot-analysis-date',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $params
    ));
    $resp = curl_exec($curl);
    curl_close($curl);

    $json = $resp;
break;

case 'robot-analysis-performance':
    $made = 'http://'.$config['serverLocalOP'].':'.$config['portLocal'].'/groot/active_bots.py';
    $params = "date=".$_POST['value'];
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

case 'view-robot-config':
    $made = 'http://'.$config['serverLocalOP'].':'.$config['portLocal'].'/groot/view_bot_config.py';
    $params = "";
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

    $json = json_decode($resp);
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
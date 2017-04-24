<?php
require_once 'config.php';
require_once 'Utils.php';
require_once 'Computation.php';
header('Access-Control-Allow-Origin: *');

$dateStart = $_GET['start'];
$dateEnd = $_GET['end'];

try {
    $start = new DateTime($dateStart);
    $end = new DateTime($dateEnd);

    $arr = [];
    $dt = $start;

    // get daily coins sum data
    $dailySumLines = file($GLOBALS['pathBotCoins'], FILE_IGNORE_NEW_LINES);
    $dailySumArr = [];
    foreach ($dailySumLines as $k => $v) {
        $vs = preg_split("/[, ]/", $v);
        if (count($vs) == 4) {
            $dailySumArr[$vs[0]] = array(
                'sum' => floatval($vs[2]),
                'hands' => $vs[3]
            );
        }
    }

    // start the date loop
    while ($end >= $dt) {
        $obj = [];
        $obj['date'] = $dt->format('Y-m-d');
        $sysChange = LogManager::fetchPropertyChangeRobotSysMod($obj['date']);

        $obj['sum'] = $dailySumArr[$obj['date']]['sum'] ?? 0;
        $obj['grossWin'] = Computation::winLost($obj['date'])['rwin'];
        $obj['sysAdd'] = $sysChange['add'];

        $obj['lost'] = Computation::winLost($obj['date'])['rlost'];
        $obj['sysDeduct'] = $sysChange['deduct'];
        $obj['rake'] = Computation::rake($obj['date'])['robot'];

        $arr[] = $obj;
        $dt->modify('+1 day');
    }

    echo json_encode($arr);
    // Utils::writeServerLog('Check system robot sum');
} catch (Throwable $t) {
    echo '[]';
}

<?php
require_once 'config.php';
require_once 'LogManager.php';

$dateStart = $_GET['start'];
$dateEnd = $_GET['end'];

try {
    $start = new DateTime($dateStart);
    $end = new DateTime($dateEnd);
} catch (Throwable $t) {
    echo '[]';
    exit(0);
}

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
    $obj['sysAdd'] = $sysChange['add'];
    $obj['sysDeduct'] = $sysChange['deduct'];

    $obj['lost'] = 0;
    $obj['rake'] = 0;
    $obj['grossWin'] = 0;
    $prlines = LogManager::fetchPokerResult($obj['date']);
    foreach ($prlines as $k => $v) {
        foreach ($v['players'] as $kk => $vv) {
            $netWin = $vv['get'] - $vv['bet'];
            if ($vv['isRobot'] == 1) {
                if ($netWin < 0) {
                    $obj['lost'] += $netWin;
                } else {
                    $obj['grossWin'] += $netWin;
                }
                $obj['rake'] += $vv['modRake'];
            }
        }
    }

    $arr[] = $obj;
    $dt->modify('+1 day');
}

echo json_encode($arr);
// Utils::writeServerLog('Check system robot sum');

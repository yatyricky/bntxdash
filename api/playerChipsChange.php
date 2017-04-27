<?php
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
$storeSum = 0;
$storeSumRecord = [];

$otherProd = [];
$otherProd['3'] = 1;
$otherProd['4'] = 1;
$otherProd['5'] = 1;
$otherProd['9'] = 1;
$otherProd['10'] = 1;
$otherProd['11'] = 1;
$otherProd['15'] = 1;
$otherProd['26'] = 1;
$otherProd['27'] = 1;
$otherProd['29'] = 1;
$otherProd['30'] = 1;
$otherProd['31'] = 1;
$otherProd['32'] = 1;
$otherProd['36'] = 1;
$otherProd['37'] = 1;
$otherProd['38'] = 1;
$otherProd['307'] = 1;
$otherProd['308'] = 1;
$otherProd['401'] = 1;
$otherProd['403'] = 1;
$otherProd['406'] = 1;
$otherProd['407'] = 1;
$otherProd['table_pump'] = 1;

$otherRecycle = [];
$otherRecycle['15'] = 1;
$otherRecycle['36'] = 1;
$otherRecycle['38'] = 1;
$otherRecycle['401'] = 1;
$otherRecycle['403'] = 1;
$otherRecycle['406'] = 1;

// start the date loop
while ($end >= $dt) {
    $obj = [];
    $obj['date'] = $dt->format('Y-m-d');

    $pclines = LogManager::fetchPropertyChange($obj['date']);

    $storeOfDay = 0;
    $storeOfDayRecord = [];

    $sumDiamonds = 0;
    $sumOtherProd = 0;
    $sumOtherRecycle = 0;
    foreach ($pclines as $k => $v) {
        $vs = explode('|', $v);

        $vs_17_account_id = intval($vs[17]);
        $vs_28_prop_value = floatval($vs[28]);
        $vs_29_prop_delta = floatval($vs[29]);

        if ($vs[26] == '1' && $vs[21] == '0') {
            // player total chips
            if (isset($storeOfDayRecord[$vs_17_account_id])) {
                $storeOfDay -= $vs_28_prop_value;
            }
            $storeOfDayRecord[$vs_17_account_id] = $vs_28_prop_value;
            $storeOfDay += $vs_28_prop_value;

            if (isset($storeSumRecord[$vs_17_account_id])) {
                $storeSum -= $storeSumRecord[$vs_17_account_id];
            }
            $storeSumRecord[$vs_17_account_id] = $vs_28_prop_value;
            $storeSum += $vs_28_prop_value;

            // diamonds prod to players
            if ($vs[27] == '402') {
                $sumDiamonds += $vs_29_prop_delta;
            }

            // other system production
            if ($vs_29_prop_delta > 0) {
                if (isset($otherProd[$vs[27]])) {
                    $sumOtherProd += $vs_29_prop_delta;
                }
            }

            // other system recycle
            if ($vs_29_prop_delta < 0) {
                if (isset($otherRecycle[$vs[27]])) {
                    $sumOtherRecycle += $vs_29_prop_delta;
                }
            }
        }
    }

    $obj['store'] = $storeOfDay;

    // win and lost
    $obj['grossWin'] = 0;
    $obj['lost'] = 0;
    $obj['rake'] = 0;
    $prlines = LogManager::fetchPokerResult($obj['date']);
    foreach ($prlines as $k => $v) {
        foreach ($v['players'] as $kk => $vv) {
            $netWin = $vv['get'] - $vv['bet'];
            if ($vv['isRobot'] == 0) {
                if ($netWin < 0) {
                    $obj['lost'] += $netWin;
                } else {
                    $obj['grossWin'] += $netWin;
                }
                $obj['rake'] += $vv['modRake'];
            }
        }
    }

    $obj['diamonds'] = $sumDiamonds;
    $obj['otherProd'] = $sumOtherProd;
    $obj['otherRecycle'] = $sumOtherRecycle;

    $arr[] = $obj;
    $dt->modify('+1 day');
}

echo json_encode($arr);
// Utils::writeServerLog('Check system robot sum');

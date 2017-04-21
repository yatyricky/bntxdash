<?php
ini_set('memory_limit', '256M');
require_once 'LogManager.php';

// $startDate = $_GET['start'];
// $endDate = $_GET['end'];

$startDate = $argv[1];
$endDate = $argv[2];

$arr = [];

$start = new DateTime($startDate);
$end = new DateTime($endDate);
$dt = $start;
while ($end >= $dt) {
    $obj = [];
    $obj['date'] = $dt->format('Y-m-d');
    $data = LogManager::fetchPokerResult($obj['date']);

    $obj['pwin'] = 0;
    $obj['plost'] = 0;
    $obj['rwin'] = 0;
    $obj['rlost'] = 0;

    foreach ($data as $k => $v) {
        foreach ($v['players'] as $kk => $vv) {
            $netWin = $vv['get'] - $vv['bet'];
            if ($vv['isRobot'] == 1) {
                if ($netWin < 0) {
                    $obj['rlost'] += $netWin;
                } else {
                    $obj['rwin'] += $netWin;
                }
            } else {
                if ($netWin < 0) {
                    $obj['plost'] += $netWin;
                } else {
                    $obj['pwin'] += $netWin;
                }
            }
        }
    }

    $arr[] = $obj;

    $dt->modify('+1 day');
}

echo "date,pwin,plost,rwin,rlost\n";
foreach ($arr as $k => $v) {
    echo $v['date'],',',$v['pwin'],',',$v['plost'],',',$v['rwin'],',',$v['rlost'],"\n";
}
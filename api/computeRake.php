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

    $obj['player'] = 0;
    $obj['robot'] = 0;
    $obj['misswin'] = 0;
    $obj['error'] = 0;

    foreach ($data as $k => $v) {
        $obj['error'] += $v['misswin'];
        foreach ($v['players'] as $kk => $vv) {
            $obj['error'] += $vv['get'] - $vv['bet'];
            if ($vv['isRobot'] == 1) {
                $obj['robot'] += $vv['modRake'];
            } else {
                $obj['player'] += $vv['modRake'];
            }
        }
        $obj['misswin'] += $v['misswin'];
    }

    $arr[] = $obj;

    $dt->modify('+1 day');
}

echo "date,player,robot,misswin,error\n";
foreach ($arr as $k => $v) {
    echo $v['date'],',',$v['player'],',',$v['robot'],',',$v['misswin'],',',$v['error'],"\n";
}
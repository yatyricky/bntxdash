<?php
ini_set('memory_limit', '1024M');
require_once 'LogManager.php';

// $startDate = $_GET['start'];
// $endDate = $_GET['end'];

$startDate = $argv[1];
$endDate = $argv[2];

$arr = [];

$start = new DateTime($startDate);
$end = new DateTime($endDate);
$dt = $start;

$list = [];
$sum = 0.0;

echo 'date,chips',PHP_EOL;

while ($end >= $dt) {
    $date = $dt->format('Y-m-d');
    $data = LogManager::fetchPropertyChangePlayerChips($date);

    $subsum = 0;
    foreach ($data as $k => $v) {
        if (isset($list[$k])) {
            $sum -= $list[$k];
        }
        $list[$k] = $v;
        $sum += $v;
        $subsum += $v;
    }

    echo $date,',',$subsum,PHP_EOL;

    $dt->modify('+1 day');
}

echo 'sum,',$sum,PHP_EOL;
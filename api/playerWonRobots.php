<?php
require_once 'config.php';
require_once 'LogManager.php';
require_once 'Utils.php';
header('Access-Control-Allow-Origin: *');

$dateStart = $_POST['start'];
$dateEnd = $_POST['end'];

$res = [];
$start = new DateTime($dateStart);
$end = new DateTime($dateEnd);
$dt = $start;

while ($end >= $dt) {
    $pwr = LogManager::fetchPlayerWonRobots($dt->format('Y-m-d'));
    foreach ($pwr as $key => $value) {
        if (isset($res[$key]) == false) {
            $res[$key] = 0;
        }
        $res[$key] += floatval($value);
    }

    $dt->modify('+1 day');
}

echo json_encode($res);

Utils::writeServerLog('Player won robots');

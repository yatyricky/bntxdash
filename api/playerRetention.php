<?php
require_once 'config.php';
require_once 'LogManager.php';
header('Access-Control-Allow-Origin: *');

$dateStart = $_GET['start'];
$dateEnd = $_GET['end'];
$days = $_GET['days'];

$start = new DateTime($dateStart);
$end = new DateTime($dateEnd);
$dt = $start;

while ($end >= $dt) {
    $dnu = LogManager::fetchNewUserIds($dt->format('Y-m-d'));
    $rrDayObj = clone $dt;
    $rrDayObj->modify('+'.$days.' day');
    $dau = LogManager::fetchActiveUserIds($rrDayObj->format('Y-m-d'));
    $intersect = array_intersect($dnu, $dau);
    echo $dt->format('Y-m-d'), ',', count($dnu), ',', count($dau), ',', count($intersect) / count($dnu),'<br/>';

    $dt->modify('+1 day');
}

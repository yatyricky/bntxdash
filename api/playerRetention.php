<?php
require_once 'LogManager.php';

$dateStart = $_GET['start'];
$dateEnd = $_GET['end'];

$start = new DateTime($dateStart);
$end = new DateTime($dateEnd);
$dt = $start;

$ret = [];
$dau = [];

while ($end >= $dt) {
    $obj = [];
    $obj['date'] = $dt->format('Y-m-d');

    $dnu = LogManager::fetchNewUserIds($obj['date']);

    $obj['dnu'] = count($dnu);

    $rrDayObj = clone $dt;

    $retentions = [];
    for ($i = 1; $i <= 30; $i++) { 
        $rrDayObj->modify('+1 day');
        $rrDayRep = $rrDayObj->format('Y-m-d');
        if (isset($dau[$rrDayRep]) == false) {
            $dau[$rrDayRep] = LogManager::fetchActiveUserIds($rrDayRep);
        }
        $intersect = array_intersect($dnu, $dau[$rrDayRep]);
        if ($obj['dnu'] == 0) {
            $retentions[] = 0;
        } else {
            $retentions[] = count($intersect) / $obj['dnu'];
        }
    }

    $obj['retentions'] = $retentions;
    $ret[] = $obj;

    $dt->modify('+1 day');
}

echo json_encode($ret);
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

// // First day
// $npwb0 = [];      // Not played with bots, 0 game
// $npwbn = [];      // Not played with bots, multiple games
// $pwbw = [];       // Played with bots won
// $pwbl1 = [];      // Played with bots lost small < 18888
// $pwbl2 = [];      // Played with bots lost much >= 1888
// // Second day;
// $npwb0c = [];     // Not played with bots, 0 game, churn
// $npwb0r = [];     // Not played with bots, 0 game, return
// $npwbnc = [];     // Not played with bots, multiple games, churn
// $npwbnr = [];     // Not played with bots, multiple games, return
// $pwbwc = [];      // Played with bots won, churn
// $pwbwr = [];      // Played with bots won, return
// $pwbl1c = [];     // Played with bots lost small < 18888, churn
// $pwbl1r = [];     // Played with bots lost small < 18888, return
// $pwbl2c = [];     // Played with bots lost much >= 1888, churn
// $pwbl2r = [];     // Played with bots lost much >= 1888, return

// for ($i = 0, $n = count($dnu); $i < $n; $i++) { 
//     if (isset($pwr[$dnu[$i]]) == false) {
//         $npwb0[$dnu[$i]] = 1;
//     } else {
//         if ($pwr[$dnu[$i]] > 0) {
//             $pwbw[$dnu[$i]] = 1;
//         } else {
//             if ($pwr[$dnu[$i]] <= -18888) {
//                 $pwbl2[$dnu[$i]] = 1;
//             } else {
//                 $pwbl1[$dnu[$i]] = 1;
//             }
//         }
//     }
// }

// function checkChurn(&$firstDay, &$churn, &$retention, $dau) {
//     foreach ($firstDay as $key => $value) {
//         if (in_array($key, $dau)) {
//             $retention[$key] = 1;
//         } else {
//             $churn[$key] = 1;
//         }
//     }
// }

// checkChurn($npwb0, $npwb0c, $npwb0r, $dau);
// checkChurn($pwbw, $pwbwc, $pwbwr, $dau);
// checkChurn($pwbl1, $pwbl1c, $pwbl1r, $dau);
// checkChurn($pwbl2, $pwbl2c, $pwbl2r, $dau);

// echo json_encode([count(array_keys($npwb0)),count(array_keys($npwbn)),count(array_keys($pwbw)),count(array_keys($pwbl1)),count(array_keys($pwbl2)),count(array_keys($npwb0r)),count(array_keys($npwbnr)),count(array_keys($pwbwr)),count(array_keys($pwbl1r)),count(array_keys($pwbl2r))]);


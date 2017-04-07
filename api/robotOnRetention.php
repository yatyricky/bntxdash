<?php
require_once 'config.php';
require_once 'LogManager.php';
require_once 'Utils.php';
header('Access-Control-Allow-Origin: *');

$date = $_POST['date'];

$nextDayObj = new DateTime($date);
$nextDayObj->modify('+1 day');
$nextDay = $nextDayObj->format('Y-m-d');

$dnu = LogManager::fetchNewUserIds($date);
$dau = LogManager::fetchActiveUserIds($nextDay);
$pwr = LogManager::fetchPlayerWonRobots($date);

// First day
$npwb0 = [];      // Not played with bots, 0 game
$npwbn = [];      // Not played with bots, multiple games
$pwbw = [];       // Played with bots won
$pwbl1 = [];      // Played with bots lost small < 18888
$pwbl2 = [];      // Played with bots lost much >= 1888
// Second day;
$npwb0c = [];     // Not played with bots, 0 game, churn
$npwb0r = [];     // Not played with bots, 0 game, return
$npwbnc = [];     // Not played with bots, multiple games, churn
$npwbnr = [];     // Not played with bots, multiple games, return
$pwbwc = [];      // Played with bots won, churn
$pwbwr = [];      // Played with bots won, return
$pwbl1c = [];     // Played with bots lost small < 18888, churn
$pwbl1r = [];     // Played with bots lost small < 18888, return
$pwbl2c = [];     // Played with bots lost much >= 1888, churn
$pwbl2r = [];     // Played with bots lost much >= 1888, return

for ($i = 0, $n = count($dnu); $i < $n; $i++) { 
    if (isset($pwr[$dnu[$i]]) == false) {
        $npwb0[$dnu[$i]] = 1;
    } else {
        if ($pwr[$dnu[$i]] > 0) {
            $pwbw[$dnu[$i]] = 1;
        } else {
            if ($pwr[$dnu[$i]] <= -18888) {
                $pwbl2[$dnu[$i]] = 1;
            } else {
                $pwbl1[$dnu[$i]] = 1;
            }
        }
    }
}

function checkChurn(&$firstDay, &$churn, &$retention, $dau) {
    foreach ($firstDay as $key => $value) {
        if (in_array($key, $dau)) {
            $retention[$key] = 1;
        } else {
            $churn[$key] = 1;
        }
    }
}

checkChurn($npwb0, $npwb0c, $npwb0r, $dau);
checkChurn($pwbw, $pwbwc, $pwbwr, $dau);
checkChurn($pwbl1, $pwbl1c, $pwbl1r, $dau);
checkChurn($pwbl2, $pwbl2c, $pwbl2r, $dau);

echo json_encode([count(array_keys($npwb0)),count(array_keys($npwbn)),count(array_keys($pwbw)),count(array_keys($pwbl1)),count(array_keys($pwbl2)),count(array_keys($npwb0r)),count(array_keys($npwbnr)),count(array_keys($pwbwr)),count(array_keys($pwbl1r)),count(array_keys($pwbl2r))]);

Utils::writeServerLog('Check robot on retention');

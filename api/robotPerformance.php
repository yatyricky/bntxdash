<?php
require_once 'config.php';
require_once 'LogManager.php';
require_once 'Utils.php';
header('Access-Control-Allow-Origin: *');

$dateStart = $_GET['start'];
$dateEnd = $_GET['end'];

$resPlayedBots = [];
$countList = [];

$start = new DateTime($dateStart);
$end = new DateTime($dateEnd);
$dt = $start;

while ($end >= $dt) {
    $pokerResult = LogManager::fetchPokerResult($dt->format('Y-m-d'));

    $playedBots = []; // Key: robot ID, Value: [won] => robot wins, [hands] => playedhands
    foreach ($pokerResult as $key => $line) {
        foreach ($line['players'] as $key1 => $lineToken) {
            if ($lineToken['isRobot'] == 1) {
                if (isset($playedBots[$lineToken['id']]) == false) {
                    $playedBots[$lineToken['id']] = array(
                        'won' => 0,
                        'hands' => 0
                    );
                }
                $playedBots[$lineToken['id']]['won'] += $lineToken['win'];
                $playedBots[$lineToken['id']]['hands'] += 1;
            }
        }
    }

    $dateFormat = $dt->format('Y-m-d');
    $resPlayedBots[$dateFormat] = $playedBots;
    for ($i = 1; $i <= 16; $i++) { 
        $countList[$dateFormat][$i] = array(
            'playedBots' => 0,  // 0: # of played bots
            'balance' => 0,     // 1: chips balance
            'lostBots' => 0,    // 2: lost # of bots
            'drawBots' => 0,    // 3: draw # of bots
            'wonBots' => 0,     // 4: won # of bots
            'lostChips' => 0,   // 5: lost chips
            'wonChips' => 0,    // 6: won chips
            'playedHands' => 0  // 7: played hands
        );
    }

    $dt->modify('+1 day');
}

if (count($resPlayedBots) > 0) {
    foreach ($resPlayedBots as $kDate => $vSet) {
        $typeList = LogManager::fetchRobotConfig($kDate);
        foreach ($vSet as $k => $v) {
            // get current bot type(key) based on bot id(k)
            $key = -1;
            if (isset($typeList[$k]) == true) {
                $key = $typeList[$k];
            }
            if ($key > 0) {
                $countList[$kDate][$key]['playedBots'] += 1;
                $countList[$kDate][$key]['balance'] += $v['won'];
                $countList[$kDate][$key]['playedHands'] += $v['hands'];
                if ($v['won'] < 0) {
                    $countList[$kDate][$key]['lostBots'] += 1;
                    $countList[$kDate][$key]['lostChips'] += $v['won'];
                } elseif ($v['won'] > 0) {
                    $countList[$kDate][$key]['wonBots'] += 1;
                    $countList[$kDate][$key]['wonChips'] += $v['won'];
                } else {
                    $countList[$kDate][$key]['drawBots'] += 1;
                }
            }
        }
    }
}    

echo json_encode($countList);

Utils::writeServerLog('Check robot performance');

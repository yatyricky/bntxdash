<?php
require_once 'config.php';
require_once 'LogManager.php';
require_once 'Utils.php';
header('Access-Control-Allow-Origin: *');

$dateStart = $_POST['start'];
$dateEnd = $_POST['end'];

$resPlayedBots = [];
$countList = [];

$start = new DateTime($dateStart);
$end = new DateTime($dateEnd);
$dt = $start;

while ($end >= $dt) {
    $pokerResult = LogManager::fetchPokerResult($dt->format('Y-m-d'));

    $playedBots = []; // Key: robot ID, Value: [won] => robot wins, [hands] => playedhands
    foreach ($pokerResult as $key => $line) {
        $lineTokens = explode(';', $line);
        foreach ($lineTokens as $key1 => $lineToken) {
            $lineTokenTokens = explode(',', trim($lineToken));
            if (count($lineTokenTokens) == 8) {
                if ($lineTokenTokens[7] == '1') {
                    $idTokens = explode(':', $lineTokenTokens[0]);
                    $keyId = intval($idTokens[0]);
                    if (isset($playedBots[$keyId]) == false) {
                        $playedBots[$keyId] = array(
                            'won' => 0,
                            'hands' => 0
                        );
                    }
                    $playedBots[$keyId]['won'] += intval($lineTokenTokens[5]);
                    $playedBots[$keyId]['hands'] += 1;
                }
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
    $rawObj = json_decode(LogManager::fetchRobotStatus('prod'), true);
    $typeList = [];

    foreach ($rawObj['robot_info'] as $key => $value) {
        $typeList[intval($value['account_id'])] = $value['config_id'];
    }

    foreach ($resPlayedBots as $kDate => $vSet) {
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

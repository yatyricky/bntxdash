<?php
require_once 'config.php';
require_once 'LogManager.php';
require_once 'Utils.php';
header('Access-Control-Allow-Origin: *');

$date = $_POST['date'];

$pokerResult = LogManager::fetchPokerResult($date);

$playedBots = []; // Key: robot ID, Value: [won] => robot wins, [hands] => playedhands
$playedBotsWithPlayers = []; // same as above

foreach ($pokerResult as $key => $line) {
    $lineTokens = explode(';', $line);
    $findPlayer = false;
    foreach ($lineTokens as $key1 => $lineToken) {
        $lineTokenTokens = explode(',', trim($lineToken));
        if (count($lineTokenTokens) == 8) {
            if ($lineTokenTokens[7] == '0') {
                $findPlayer = true;
            }
        }
    }
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
                if ($findPlayer == true) {
                    if (isset($playedBotsWithPlayers[$keyId]) == false) {
                        $playedBotsWithPlayers[$keyId] = array(
                            'won' => 0,
                            'hands' => 0
                        );
                    }
                    $playedBotsWithPlayers[$keyId]['won'] += intval($lineTokenTokens[5]);
                    $playedBotsWithPlayers[$keyId]['hands'] += 1;
                }
            }
        }
    }
}

$rawObj = json_decode(LogManager::fetchRobotStatus('prod'), true);
$countList = [];
$typeList = [];

foreach ($rawObj['robot_info'] as $key => $value) {
    $typeList[intval($value['account_id'])] = $value['config_id'];
    if (isset($countList[$value['config_id']]) == false) {
        $countList[$value['config_id']] = array(
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
}

$count = 0;
foreach ($playedBots as $k => $v) {
    $key = -1;
    if (isset($typeList[$k]) == true) {
        $key = $typeList[$k];
    }
    if (isset($countList[$key]) == false) {
        $countList[$key] = array(
            'playedBots' => 0,
            'balance' => 0,
            'lostBots' => 0,
            'drawBots' => 0,
            'wonBots' => 0,
            'lostChips' => 0,
            'wonChips' => 0,
            'playedHands' => 0
        );
    }
    $countList[$key]['playedBots'] += 1;
    $countList[$key]['balance'] += $v['won'];
    $countList[$key]['playedHands'] += $v['hands'];
    if ($v['won'] < 0) {
        $countList[$key]['lostBots'] += 1;
        $countList[$key]['lostChips'] += $v['won'];
    } elseif ($v['won'] > 0) {
        $countList[$key]['wonBots'] += 1;
        $countList[$key]['wonChips'] += $v['won'];
    } else {
        $countList[$key]['drawBots'] += 1;
    }
    $count += 1;
}

echo json_encode($countList);

Utils::writeServerLog('Check robot performance');

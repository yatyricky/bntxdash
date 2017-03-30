<?php
require_once 'config.php';
require_once 'LogManager.php';

$date = $_GET['date'];

$pokerResult = LogManager::fetchPokerResult($date);

$sum = 0;
$lol = 0;

foreach ($pokerResult as $k => $v) {
    // v = 415587:H10HA ,poker_type:-1,10,0,0,-10,3,1;416311:DQ DK ,poker_type:-1,0,0,0,0,3,1;416320:D5 D10,poker_type:6,1029,2017,2017,988,4,1;555322:HQ CK ,poker_type:4,1029,0,0,-1029,4,0;
    $vs = explode(';', $v);
    foreach ($vs as $k1 => $v1) {
        // v1 = 415587:H10HA ,poker_type:-1,10,0,0,-10,3,1
        $v1s = explode(',', $v1);
        // 0 | id:handpoker |
        // 1 | poker_type |
        // 2 | bet | bet
        // 3 | get1 | won chips (rake excluded)
        // 4 | get2 | final won chips (anti-addiction excluded)
        // 5 | win | balance
        // 6 | status | 1 leave,2 standup,3 fold,4 showdown
        // 7 | isrobotflg | 1: bot, 0: player
        if (count($v1s) > 7) {
            $sum += intval($v1s[5]);
        } elseif (count($v1s) > 0 && strlen($v1s[0]) > 0) {
            // echo $k,': ','len(',count($v1s),')',$v1,'<br/>';
            $lol += 1;
        }
    }
}

// echo $sum;
echo $lol/count($pokerResult)*100;
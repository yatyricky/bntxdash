<?php
require_once 'LogManager.php';

class Computation {

    public static function rake($date) {
        $obj = [];
        $data = LogManager::fetchPokerResult($date);

        $obj['player'] = 0;
        $obj['robot'] = 0;
        $obj['misswin'] = 0;
        $obj['error'] = 0;

        foreach ($data as $k => $v) {
            $obj['error'] += $v['misswin'];
            foreach ($v['players'] as $kk => $vv) {
                $obj['error'] += $vv['get'] - $vv['bet'];
                if ($vv['isRobot'] == 1) {
                    $obj['robot'] += $vv['modRake'];
                } else {
                    $obj['player'] += $vv['modRake'];
                }
            }
            $obj['misswin'] += $v['misswin'];
        }

        return $obj;
    }

    public static function winLost($date) {
        $obj = [];
        $data = LogManager::fetchPokerResult($date);

        $obj['pwin'] = 0;
        $obj['plost'] = 0;
        $obj['rwin'] = 0;
        $obj['rlost'] = 0;

        foreach ($data as $k => $v) {
            foreach ($v['players'] as $kk => $vv) {
                $netWin = $vv['get'] - $vv['bet'];
                if ($vv['isRobot'] == 1) {
                    if ($netWin < 0) {
                        $obj['rlost'] += $netWin;
                    } else {
                        $obj['rwin'] += $netWin;
                    }
                } else {
                    if ($netWin < 0) {
                        $obj['plost'] += $netWin;
                    } else {
                        $obj['pwin'] += $netWin;
                    }
                }
            }
        }

        return $obj;
    }

}
<?php
require_once 'config.php';
require_once 'Utils.php';

class LogManager {

    public static function fetchActiveUserIds($date) {
        $fPath = $GLOBALS['grootDir'].DIRECTORY_SEPARATOR.'dau_ids'.DIRECTORY_SEPARATOR.'dau_id_'.$date.'.txt';
        if (file_exists($fPath)) {
            $arr = file($fPath, FILE_IGNORE_NEW_LINES);
        } else {
            $rawLoginPath = $GLOBALS['grootDir'].DIRECTORY_SEPARATOR.'player_login'.DIRECTORY_SEPARATOR.$date.'.txt';
            if (file_exists($rawLoginPath)) {
                $tempSet = [];
                $lines = file($rawLoginPath, FILE_IGNORE_NEW_LINES);
                foreach ($lines as $k => $v) {
                    $tokens = explode('|', $v);
                    if (isset($tempSet[$tokens[17]]) == false && $tokens[21] != '1') {
                        $tempSet[$tokens[17]] = 1;
                    }
                }
                $arr = array_keys($tempSet);
                file_put_contents($fPath, join(PHP_EOL, $arr), LOCK_EX);
            } else {
                $arr = [];
            }
        }
        return $arr;
    }

    public static function fetchNewUserIds($date) {
        $fPath = $GLOBALS['grootDir'].DIRECTORY_SEPARATOR.'register'.DIRECTORY_SEPARATOR.$date.'_accountID.txt';
        if (file_exists($fPath)) {
            $lines = file($fPath, FILE_IGNORE_NEW_LINES);
            $arr = [];
            for ($i = 0, $n = count($lines); $i < $n; $i++) { 
                $tokens = explode(',', trim($lines[$i]));
                if (count($tokens) > 0) {
                    $arr[] = $tokens[0];
                }
            }
        } else {
            $rawPath = $GLOBALS['grootDir'].DIRECTORY_SEPARATOR.'register'.DIRECTORY_SEPARATOR.$date.'.txt';
            if (file_exists($rawPath)) {
                $keySet = [];
                $lines = file($rawPath, FILE_IGNORE_NEW_LINES);
                for ($i = 0, $n = count($lines); $i < $n; $i++) { 
                    $playerId = explode('|', trim($lines[$i]))[13];
                    if (isset($keySet[$playerId]) == false) {
                        $keySet[$playerId] = 1;
                    }
                }
                $arr = array_keys($keySet);
                file_put_contents($fPath, join(PHP_EOL, $arr), LOCK_EX);
            } else {
                $arr = [];
            }
        }
        return $arr;
    }

    /**
     *  [
     *      {
     *          "players": [
     *              {
     *                  "id": old1 | new1,
     *                  "handPoker": old2 | new2,
     *                  "bet": old5 | new3,
     *                  "get": old6 / 0.95 | new4,
     *                  "modRake": old6 / 19.0 | new5,
     *                  "modAntiadc": old6 - old7 | new6,
     *                  "modItem": 0 | new7,
     *                  "win": old8 | new8,
     *                  "status": old9 | new9,
     *                  "isRobot": old10 | new10
     *              }
     *          ],
     *          "misswin": 0 | row14
     *      }
     *  ]
     */
    public static function fetchPokerResult($date) {
        $oldC11Path = $GLOBALS['grootDir'].DIRECTORY_SEPARATOR.'poker_result'.DIRECTORY_SEPARATOR.$date.'_c11.txt';
        if (file_exists($oldC11Path)) {
            // old log, extracted column 11
            $arr = [];
            $c11Contents = file($oldC11Path, FILE_IGNORE_NEW_LINES);
            foreach ($c11Contents as $k => $v) {
                $obj['players'] = [];
                $obj['misswin'] = 0;
                $vs = explode(';', $v);
                foreach ($vs as $kk => $vv) {
                    if (strlen($vv) > 0) {
                        $vvs = preg_split("/[,:]/", $vv);
                        $obj['players'][] = array(
                            'id' => intval($vvs[0]),
                            'handPoker' => $vvs[1],
                            'bet' => floatval($vvs[4]),
                            'get' => floatval($vvs[5]) / 0.95,
                            'modRake' => floatval($vvs[5]) / 19.0,
                            'modAntiadc' => floatval($vvs[5]) - floatval($vvs[6]),
                            'modItem' => 0,
                            'win' => floatval($vvs[7]),
                            'status' => intval($vvs[8]),
                            'isRobot' => intval($vvs[9])
                        );
                    }
                }
                $arr[] = $obj;
            }
            return $arr;
        } else {
            $rawPath = $GLOBALS['grootDir'].DIRECTORY_SEPARATOR.'poker_result'.DIRECTORY_SEPARATOR.$date.'.txt';
            if (file_exists($rawPath)) {
                $arr = [];
                $lines = file($rawPath, FILE_IGNORE_NEW_LINES);
                foreach ($lines as $k => $v) {
                    $obj['players'] = [];
                    $obj['misswin'] = 0;

                    $vs = explode('|', $v);
                    $c11s = explode(';', $vs[10]);
                    foreach ($c11s as $kk => $vv) {
                        if (strlen($vv) > 0) {
                            $vvs = preg_split("/[,:]/", $vv);
                            if (count($vs) < 17) {
                                // old log, not extracted
                                $obj['players'][] = array(
                                    'id' => intval($vvs[0]),
                                    'handPoker' => $vvs[1],
                                    'bet' => floatval($vvs[4]),
                                    'get' => floatval($vvs[5]) / 0.95,
                                    'modRake' => floatval($vvs[5]) / 19.0,
                                    'modAntiadc' => floatval($vvs[5]) - floatval($vvs[6]),
                                    'modItem' => 0,
                                    'win' => floatval($vvs[7]),
                                    'status' => intval($vvs[8]),
                                    'isRobot' => intval($vvs[9])
                                );
                            } else {
                                // new log
                                $obj['players'][] = array(
                                    'id' => intval($vvs[0]),
                                    'handPoker' => $vvs[1],
                                    'bet' => floatval($vvs[2]),
                                    'get' => floatval($vvs[3]),
                                    'modRake' => floatval($vvs[4]),
                                    'modAntiadc' => floatval($vvs[5]),
                                    'modItem' => floatval($vvs[6]),
                                    'win' => floatval($vvs[3]) - floatval($vvs[2]) - floatval($vvs[4]) - floatval($vvs[5]) + floatval($vvs[6]),
                                    'status' => intval($vvs[8]),
                                    'isRobot' => intval($vvs[9])
                                );
                            }
                        }
                    }
                    if (count($vs) >= 17) {
                        $obj['misswin'] = floatval($vs[13]);
                    }
                    $arr[] = $obj;
                }
                return $arr;
            } else {
                // no log file, return empty
                return [];
            }
        }

    }

    public static function fetchPropertyChangePlayerChips($date) {
        $fPath = $GLOBALS['grootDir'].DIRECTORY_SEPARATOR.'property_change'.DIRECTORY_SEPARATOR.$date.'.txt';
        $arr = [];
        if (file_exists($fPath)) {
            $lines = file($fPath, FILE_IGNORE_NEW_LINES);
            foreach ($lines as $k => $v) {
                $vs = explode('|', $v);
                if ($vs[26] == '1' && $vs[21] == '0') {
                    $arr[intval($vs[17])] = floatval($vs[28]);
                }
            }
        }
        return $arr;
    }

    public static function fetchPlayerWonRobots($date) {
        $data = self::fetchPokerResult($date);
        if (count($data) > 0) {
            $robotList = [];
            $winnerList = [];

            foreach ($data as $k => $v) {
                // find if robot exists on the game table
                $found = 0;
                $tempSet = [];

                foreach ($v['players'] as $kk => $vv) {
                    $tempSet[$vv['id']] = $vv['win'];
                    if ($vv['isRobot'] == 1) {
                        $robotList[$vv['id']] = 1;
                        $found = 1;
                    }
                }

                if ($found == 1) {
                    foreach ($tempSet as $key => $value) {
                        if (isset($robotList[$key]) == false) {
                            if (isset($winnerList[$key]) == false) {
                                $winnerList[$key] = 0;
                            }
                            $winnerList[$key] += $value;
                        }
                    }
                }
            }
            return $winnerList;
        } else {
            return [];
        }
    }

    public static function fetchRobotStatus($svr) {
        $server = $GLOBALS['serverBeta'];
        if ($svr == 'prod') {
            $server = $GLOBALS['serverProdZH'];
        }
        $made = 'http://'.$server.':'.$GLOBALS['portGS'].'/account/get_robot_state_info';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $made,
            CURLOPT_USERAGENT => 'Query robot info',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => ''
        ));
        $resp = curl_exec($curl);
        curl_close($curl);
        Utils::writeServerLog($made);

        return $resp;
    }

    public static function fetchRobotConfig($date) {
        $path = $GLOBALS['robotConfigDir'].DIRECTORY_SEPARATOR.'robot_ids_'.$date.'.csv';
        if (file_exists($path) == false) {
            $path = $GLOBALS['robotConfigDir'].DIRECTORY_SEPARATOR.'robot_ids.csv';
        }
        $arr = [];
        $lines = file($path, FILE_IGNORE_NEW_LINES);
        foreach ($lines as $k => $v) {
            $vs = explode(',', $v);
            $arr[$vs[0]] = $vs[1];
        }
        return $arr;
    }
}

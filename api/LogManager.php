<?php
require_once 'config.php';
require_once 'Utils.php';

class LogManager {

    public static function fetchActiveUserIds($date) {
        $fPath = $GLOBALS['grootDir'].DIRECTORY_SEPARATOR.'dau_ids'.DIRECTORY_SEPARATOR.'dau_id_'.$date.'.txt';
        if (file_exists($fPath)) {
            $lines = file($fPath);
            $arr = [];
            for ($i = 0, $n = count($lines); $i < $n; $i++) { 
                $tokens = explode(',', trim($lines[$i]));
                if (count($tokens) > 0) {
                    $arr[] = $tokens[0];
                }
            }
        } else {
            $rawLoginPath = $GLOBALS['grootDir'].DIRECTORY_SEPARATOR.'player_login'.DIRECTORY_SEPARATOR.$date.'.txt';
            $rawLogoutPath = $GLOBALS['grootDir'].DIRECTORY_SEPARATOR.'player_logout'.DIRECTORY_SEPARATOR.$date.'.txt';
            if (file_exists($rawLoginPath) && file_exists($rawLogoutPath)) {
                $arr = [];
                $lines = file($rawLoginPath);
                for ($i = 0, $n = count($lines); $i < $n; $i++) {
                    $tokens = explode('|', trim($lines[$i]));
                    if (isset($arr[$tokens[17]]) == false && $tokens[21] != '1') {
                        $arr[$tokens[17]] = 1;
                    }
                }

                $lines = file($rawLogoutPath);
                for ($i = 0, $n = count($lines); $i < $n; $i++) { 
                    $tokens = explode('|', trim($lines[$i]));
                    if (isset($arr[$tokens[17]]) == false && $tokens[21] != '1') {
                        $arr[$tokens[17]] = 1;
                    }
                }
                file_put_contents($fPath, join(PHP_EOL, array_keys($arr)), LOCK_EX);
            } else {
                $arr = [];
            }
        }
        return $arr;
    }

    public static function fetchNewUserIds($date) {
        $fPath = $GLOBALS['grootDir'].DIRECTORY_SEPARATOR.'register'.DIRECTORY_SEPARATOR.$date.'_accountID.txt';
        if (file_exists($fPath)) {
            $lines = file($fPath);
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
                $arr = [];
                $lines = file($rawPath);
                for ($i = 0, $n = count($lines); $i < $n; $i++) { 
                    $playerId = explode('|', trim($lines[$i]))[13];
                    if (isset($arr[$playerId]) == false) {
                        $arr[$playerId] = 1;
                    }
                }
                file_put_contents($fPath, join(PHP_EOL, array_keys($arr)), LOCK_EX);
            } else {
                $arr = [];
            }
        }
        return $arr;
    }

    public static function fetchPokerResult($date) {
        $fPath = $GLOBALS['grootDir'].DIRECTORY_SEPARATOR.'poker_result'.DIRECTORY_SEPARATOR.$date.'_c11.txt';
        if (!file_exists($fPath)) {
            $rawPath = $GLOBALS['grootDir'].DIRECTORY_SEPARATOR.'poker_result'.DIRECTORY_SEPARATOR.$date.'.txt';
            if (!file_exists($rawPath)) {
                return [];
            } else {
                $c11Contents = [];
                $lines = file($rawPath);
                for ($i = 0, $n = count($lines); $i < $n; $i++) { 
                    $tokens = explode('|', trim($lines[$i]));
                    if (count($tokens) > 10) {
                        $c11Contents[] = $tokens[10];
                    }
                }
                file_put_contents($fPath, join(PHP_EOL, $c11Contents), LOCK_EX);
                return $c11Contents;
            }
        } else {
            $lines = file($fPath);
            $arr = [];
            for ($i = 0, $n = count($lines); $i < $n; $i++) { 
                $arr[] = trim($lines[$i]);
            }
            return $arr;
        }

    }

    public static function fetchPlayerWonRobots($date) {
        $fPath = $GLOBALS['grootDir'].DIRECTORY_SEPARATOR.'player_win_robots_logs'.DIRECTORY_SEPARATOR.'player_win_robots_'.$date.'.csv';
        if (file_exists($fPath)) {
            $lines = file($fPath);
            for ($i = 0, $n = count($lines); $i < $n; $i++) { 
                $tokens = explode(',', trim($lines[$i]));
                $obj[$tokens[0]] = floatval($tokens[1]);
            }
            return $obj;
        } else {
            $data = self::fetchPokerResult($date);
            if (count($data) > 0) {
                $robotList = [];
                $winnerList = [];

                for ($i = 0, $n = count($data); $i < $n; $i++) {
                    $players = explode(';', $data[$i]);

                    // find if robot exists on the game table
                    $found = 0;
                    $tempSet = [];
                    for ($j = 0, $m = count($players); $j < $m; $j++) { 
                        $tokens = explode(',', trim($players[$j]));
                        if (count($tokens) == 8) {
                            $idTokens = explode(':', $tokens[0]);
                            $tempSet[$idTokens[0]] = floatval($tokens[5]);
                            if (intval($tokens[7]) == 1) {
                                $robotList[$idTokens[0]] = 1;
                                $found = 1;
                            }
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

                $output = '';
                foreach ($winnerList as $key => $value) {
                    $output .= $key.','.$value."\n";
                }

                file_put_contents($fPath, $output, LOCK_EX);
                return $winnerList;
            } else {
                return [];
            }
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
}

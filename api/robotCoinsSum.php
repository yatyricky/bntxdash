<?php
require_once 'config.php';
require_once 'writeServerLog.php';
require_once 'tailCustom.php';
header('Access-Control-Allow-Origin: *');

$list = explode("\n", tailCustom($GLOBALS['pathBotCoins'], 10));
$array = [];
for ($i = 0, $n = count($list); $i < $n; $i++) { 
    $tokens = explode(',', trim($list[$i]));
    if (count($tokens) == 3) {
        $obj['time'] = $tokens[0];
        $obj['value'] = $tokens[1];
        $obj['played'] = $tokens[2];
        $array[] = $obj;
    }
}

$made = "Check system robot sum";

echo json_encode($array);

writeServerLog($made);
?>
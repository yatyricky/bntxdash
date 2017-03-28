<?php
require_once 'LogManager.php';
header('Access-Control-Allow-Origin: *');

echo LogManager::fetchRobotStatus($_POST['server']);

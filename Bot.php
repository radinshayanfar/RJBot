<?php

use Update\Action;

header("Content-Type: application/json");

define('API_TOKEN', file_get_contents('TOKEN'));

require_once("requirements.php");

$content = file_get_contents("php://input");
$update = json_decode($content, true);

// Database connection
try {
    $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $db->updateUserMessageCount($update);
} catch (Exception $e) {
}

$api = new TelegramAPI(API_TOKEN);

new Action($update, $api, $db);
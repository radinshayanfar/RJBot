<?php

use Update\Action;

header("Content-Type: application/json");

require_once("requirements.php");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$content = file_get_contents("php://input");
$update = json_decode($content, true);

// Database connection
try {
    $db = new Database($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);
    $db->updateUserMessageCount($update);
} catch (Exception $e) {
}
$api = new TelegramAPI($_ENV['TOKEN']);

new Action($update, $api, $db);
<?php

use Update\Action;

header("Content-Type: application/json");

define('API_TOKEN', file_get_contents('TOKEN'));

require_once('TelegramAPI.php');
require_once('./Database/DatabaseConfig.php');
require_once('./Database/Database.php');
require_once('Helpers/DebugHelper.php');
require_once('Helpers/URLHelper.php');
require_once('Helpers/StringHelper.php');
require_once('Helpers/Helper.php');
require_once('./Core/URLRedirect.php');
include_once('./Core/MediaType.php');
require_once('./Core/Video.php');
require_once('./Core/SingleAudio.php');
require_once('./Core/Album.php');
require_once('./Update/Action.php');

$content = file_get_contents("php://input");
$update = json_decode($content, true);

// Database connection
try {
    $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $db->updateUserMessageCount($update);
} catch (Exception $e) {}

$api = new TelegramAPI(API_TOKEN);

new Action($update, $api, $db);
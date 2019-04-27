<?php

header("Content-Type: application/json");

define('API_TOKEN', file_get_contents('TOKEN'));

require_once('TelegramAPI.php');
require_once('DatabaseConfig.php');
require_once('Database.php');
require_once('Helpers/DebugHelper.php');
require_once('Helpers/URLHelper.php');
require_once('Helpers/StringHelper.php');
require_once('./Core/URLRedirect.php');
require_once('./Core/Video.php');
require_once('./Core/SingleAudio.php');
require_once('./Core/Album.php');
require_once('Command.php');

$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (isset($content['edited_message'])) {
    exit;
}
// Connection to database
try {
    $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $db->updateCount($update);
} catch (Exception $e) {}

$bot = new TelegramAPI(API_TOKEN);
if (!isset($update['message']['text'])) {
    $chat_id = $update['message']['chat']['id'];
    $resp = array('chat_id' => $chat_id, 'text' => 'Please send a Radio Javan media link.');
    $bot->webhookSend('sendMessage', $resp);
    exit();
}

$text = $update['message']['text'];
switch ($text) {
    case '/start':
        \Command\start($update);
        break;

    default:
        \Command\downloader($update);
        break;
}
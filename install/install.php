<?php

echo "+ Running composer update...\n";
exec('composer update');

require_once("vendor/autoload.php");
require_once("TelegramAPI.php");

// Reading .env file
echo "Parsing .env file...\n";
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Create database connection
echo "+ Connecting to database ...\n";
$db = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME']);
if ($db->connect_error) {
    die("Database connection failed: " . $db->connect_error);
}

echo "+ Creating database tables...\n";
$query = file_get_contents("install/install.sql");
$split = preg_split('~;~', $query);
$split = array_slice($split, 0, count($split) - 1);
foreach ($split as $q)
    $db->query($q) or die($db->error);


echo "Enter complete url to project directory in order to set webhook url in Telegram API (example: https://mydomain.com/mybot/):\n";
echo "[Leave it blank to ignore]\n";
$url = trim(fgets(STDIN));
if ($url) {
    $api = new TelegramAPI($_ENV['TOKEN']);
    $api->postSend('setWebhook', ['url' => $url . '/Bot.php']);
}

echo "Done\n";

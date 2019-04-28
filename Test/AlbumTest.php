<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('../Core/Album.php');
include_once('../DatabaseConfig.php');
include_once('../Database.php');

$url = new \Core\URLRedirect('https://rjapp.me/ma/KN7LDa7J');
$dbc = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
var_dump($dbc->autoIncrementStart());
$album = new \Core\Album($url, $dbc->autoIncrementStart());
var_dump($album->getLinks());

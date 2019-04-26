<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('../Core/Album.php');

$url = new \Core\URLRedirect('https://rjapp.me/ma/KN7LDa7J');
$album = new \Core\Album($url);
var_dump($album->getLinks());
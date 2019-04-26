<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('../Core/Video.php');

use Core\URLRedirect;
use \Core\Video;

$url = new URLRedirect('https://rjapp.me/v/EbVkKXP6');
$media = new Video($url);
var_dump($media->getLinks());

<?php

use Core\Audio;
use Core\SingleAudio;

include_once('../Core/SingleAudio.php');

$url = new Core\URLRedirect('https://rjapp.me/m/4qLDnNqW');
$url2 = new Core\URLRedirect('https://rjapp.me/m/LvAWg7qx');
$url3 = new Core\URLRedirect('https://rjapp.me/p/krQgRK8N');
$music = new SingleAudio($url, Audio::MP3_HOST);
$music2 = new SingleAudio($url2, Audio::MP3_HOST);
$podcast = new SingleAudio($url3, Audio::PODCAST_HOST);
var_dump($music->getLinks());
var_dump($music2->getLinks());
var_dump($podcast->getLinks());
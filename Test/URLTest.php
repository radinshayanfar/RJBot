<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('../Core/URLRedirect.php');
use \Core\URLRedirect;

$url = new URLRedirect('https://rjapp.me/ma/KN7LDa7J');
echo $url->getRedirectedURL() . '<br />';
echo $url->getData();
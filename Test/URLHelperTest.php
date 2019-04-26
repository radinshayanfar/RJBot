<?php

include_once('../Helpers/URLHelper.php');
use \Helper\URLHelper;

var_dump(URLHelper::validateURL('https://google.com/'));
var_dump(URLHelper::validateURL('google.com'));
var_dump(URLHelper::validateURL('This is for test'));
var_dump(URLHelper::isHostEquals('https://www.radiojavan.com/mp3s/album/Moein-Tavalod-Eshgh', 'www.radiojavan.com'));
var_dump(URLHelper::isHostEquals('https://www.radiojavan.com/mp3s/album/Moein-Tavalod-Eshgh', 'www.apple.com'));

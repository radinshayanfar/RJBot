<?php

include_once('../Helpers/StringHelper.php');
use \Helper\StringHelper;

echo StringHelper::startsWith('/mp3s/album/Moein-Tavalod-Eshgh', '/mp3s/album/') . '<br />';
var_dump(StringHelper::startsWith('/mp3s/album/Moein-Tavalod-Eshgh', '/podcast/album/'));

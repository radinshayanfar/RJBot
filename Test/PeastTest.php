<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('xdebug.var_display_max_depth', '-1');
ini_set('xdebug.var_display_max_children', '-1');
ini_set('xdebug.var_display_max_data', '-1');

require_once("../vendor/autoload.php");

$source = file_get_contents('PeastTestScript.js'); //JavaScript code
$options = array();
$ast = Peast\Peast::latest($source, $options)->parse();

var_dump($ast->getBody()[0]->getExpression()->getRight()->getValue());
var_dump($ast->getBody()[0]->getExpression()->getLeft()->getProperty()->getName());

// var_dump($ast->getBody()[7]->getExpression()->getRight()->getElements());
var_dump($ast->getBody()[7]->getExpression()->getLeft()->getProperty()->getName());
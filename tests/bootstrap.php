<?php

require_once __DIR__ . "/../vendor/autoload.php";

$_SERVER['REQUEST_URI']    = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SERVER_NAME']    = 'localhost';
$_SERVER['PHP_SELF']       = '/index.php';
$_SERVER['DOCUMENT_ROOT']  = __DIR__;

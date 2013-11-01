<?php

define('TEST_ROOT', __DIR__);

$autoload = TEST_ROOT. "/../vendor/autoload.php";

if (! @include_once $autoload) {
    shell_exec("php ". TEST_ROOT. "/../bin/composer.phar install");
    require_once $autoload;
}

$_SERVER['REQUEST_URI']    = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SERVER_NAME']    = 'localhost';
$_SERVER['PHP_SELF']       = '/index.php';
$_SERVER['DOCUMENT_ROOT']  = TEST_ROOT;

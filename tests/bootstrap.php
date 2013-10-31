<?php

$autoload = __DIR__. "/../vendor/autoload.php";

if (! @include_once $autoload) {
    shell_exec("php ". __DIR__. "/../bin/composer.phar install");
    require_once $autoload;
}

$_SERVER['REQUEST_URI']    = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SERVER_NAME']    = 'localhost';
$_SERVER['PHP_SELF']       = '/index.php';
$_SERVER['DOCUMENT_ROOT']  = __DIR__;

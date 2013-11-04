<?php

define('TEST_ROOT', __DIR__);

$autoload = TEST_ROOT. "/../vendor/autoload.php";

if (! @include_once $autoload) {
    shell_exec("php ". TEST_ROOT. "/../bin/composer.phar install");
    require_once $autoload;
}

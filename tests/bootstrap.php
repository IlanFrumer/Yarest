<?php

require_once "constants.php";

$autoload = TEST_ROOT. "/../vendor/autoload.php";

if (! @include_once $autoload) {
    shell_exec("php ". TEST_ROOT. "/../bin/composer.phar install");
    require_once $autoload;
}

<?php

declare(strict_types=1);

use DoctrineFixture\FixtureManager;

chdir(__DIR__);

$loader = null;
if (file_exists('../vendor/autoload.php')) {
    $loader = include '../vendor/autoload.php';
} else {
    throw new RuntimeException('vendor/autoload.php could not be found. Did you run `php composer.phar install`?');
}

FixtureManager::start();

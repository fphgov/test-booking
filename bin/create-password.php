<?php

declare(strict_types=1);

opcache_invalidate(__FILE__, true);

if (PHP_SAPI !== 'cli') {
    return false;
}

$options = getopt("p:");

chdir(__DIR__ . '/../');

require 'vendor/autoload.php';

$config = include 'config/config.php';

$hash = password_hash($options['p'], PASSWORD_BCRYPT, $config['password']);

echo $hash . "\n";

<?php

declare(strict_types=1);

define('BASIC_PATH', dirname(__FILE__, 2));

// Delegate static file requests back to the PHP built-in webserver
if (PHP_SAPI === 'cli-server' && $_SERVER['SCRIPT_FILENAME'] !== __FILE__) {
    return false;
}

chdir(dirname(__DIR__));

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createUnsafeMutable(BASIC_PATH);
$dotenv->load();

define('UPLOAD_PATH', BASIC_PATH . getenv('PATH_UPLOAD'));

(function () {
    $container = require 'config/container.php';

    $app     = $container->get(Mezzio\Application::class);
    $factory = $container->get(Mezzio\MiddlewareFactory::class);

    // Import programmatic/declarative middleware pipeline and routing
    // configuration statements
    (require 'config/pipeline.php')($app, $factory, $container);
    (require 'config/routes.php')($app, $factory, $container);

    $app->run();
})();

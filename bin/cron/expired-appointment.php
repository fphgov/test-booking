<?php

declare(strict_types=1);

opcache_invalidate(__FILE__, true);

if (PHP_SAPI !== 'cli') {
    return false;
}

chdir(__DIR__ . '/../../');

use App\Service\AppointmentServiceInterface;

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createUnsafeMutable(dirname(__DIR__, 2));
$dotenv->load();

$config    = include 'config/config.php';
$container = require 'config/container.php';

$appointmentService = $container->get(AppointmentServiceInterface::class);

$appointmentService->clearExpiredData();

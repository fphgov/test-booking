<?php

declare(strict_types=1);

define('BASIC_PATH', dirname(__FILE__, 2));

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Symfony\Component\Console\Helper\HelperSet;

require_once BASIC_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createUnsafeMutable(BASIC_PATH);
$dotenv->load();

$container = require __DIR__ . '/container.php';

return new HelperSet([
  'em' => new EntityManagerHelper(
      $container->get(EntityManagerInterface::class)
  ),
]);

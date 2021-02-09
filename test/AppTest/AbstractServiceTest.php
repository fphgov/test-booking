<?php

declare(strict_types=1);

namespace AppTest;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

use function dirname;

abstract class AbstractServiceTest extends TestCase
{
    /** @var ContainerInterface */
    protected static $container;

    public static function setUpBeforeClass(): void
    {
        static::initContainer();
    }

    protected static function initContainer(): void
    {
        static::$container = require dirname(__FILE__, 3) . '/config/container.php';
    }
}

<?php

declare(strict_types=1);

namespace Jwt;

/**
 * The configuration provider for the Jwt module
 *
 * @see https://docs.laminas.dev/laminas-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * Returns the container dependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            'invokables' => [],
            'factories'  => [
                Handler\JwtAuthMiddleware::class => Handler\JwtAuthMiddlewareFactory::class,
                Handler\TokenHandler::class      => Handler\TokenHandlerFactory::class,
            ],
        ];
    }
}

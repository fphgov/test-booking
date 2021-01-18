<?php

declare(strict_types=1);

namespace Jwt\Handler;

use Psr\Container\ContainerInterface;
use RuntimeException;
use Tuupola\Middleware\JwtAuthentication;

use function getenv;

class JwtAuthMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): JwtAuthMiddleware
    {
        $config = $container->has('config') ? $container->get('config') : [];

        if (! isset($config['jwt'])) {
            throw new RuntimeException('Missing JWT configuration');
        }

        $auth = new JwtAuthentication([
            "secure"    => getenv('NODE_ENV') !== 'development',
            "relaxed"   => ["localhost"],
            "secret"    => $config['jwt']['auth']['secret'],
            "attribute" => JwtAuthMiddleware::class,
        ]);

        return new JwtAuthMiddleware(
            $auth
        );
    }
}

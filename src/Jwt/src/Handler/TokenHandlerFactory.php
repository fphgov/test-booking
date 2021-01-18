<?php

declare(strict_types=1);

namespace Jwt\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use RuntimeException;

class TokenHandlerFactory
{
    public function __invoke(ContainerInterface $container): TokenHandler
    {
        $config = $container->has('config') ? $container->get('config') : [];

        if (! isset($config['jwt'])) {
            throw new RuntimeException('Missing JWT configuration');
        }

        return new TokenHandler(
            $container->get(EntityManagerInterface::class),
            $config['jwt']
        );
    }
}

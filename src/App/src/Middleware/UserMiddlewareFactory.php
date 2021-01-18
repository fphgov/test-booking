<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Service\UserServiceInterface;
use Psr\Container\ContainerInterface;

class UserMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): UserMiddleware
    {
        return new UserMiddleware(
            $container->get(UserServiceInterface::class)
        );
    }
}

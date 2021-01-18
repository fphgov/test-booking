<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Interop\Container\ContainerInterface;

final class UserServiceFactory
{
    /**
     * @return UserService
     */
    public function __invoke(ContainerInterface $container)
    {
        return new UserService(
            $container->get(EntityManagerInterface::class)
        );
    }
}

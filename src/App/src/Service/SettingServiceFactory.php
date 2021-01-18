<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Interop\Container\ContainerInterface;

final class SettingServiceFactory
{
    /**
     * @return SettingService
     */
    public function __invoke(ContainerInterface $container)
    {
        return new SettingService(
            $container->get(EntityManagerInterface::class)
        );
    }
}

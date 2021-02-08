<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Interop\Container\ContainerInterface;

final class AppointmentServiceFactory
{
    /**
     * @return AppointmentService
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->has('config') ? $container->get('config') : [];

        return new AppointmentService(
            $config,
            $container->get(EntityManagerInterface::class)
        );
    }
}

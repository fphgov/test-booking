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
        return new AppointmentService(
            $container->get(EntityManagerInterface::class)
        );
    }
}

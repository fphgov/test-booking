<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Interop\Container\ContainerInterface;

final class ReservationServiceFactory
{
    /**
     * @return ReservationService
     */
    public function __invoke(ContainerInterface $container)
    {
        return new ReservationService(
            $container->get(EntityManagerInterface::class)
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Handler\Appointment;

use App\Service\AppointmentServiceInterface;
use App\Service\ReservationServiceInterface;
use Interop\Container\ContainerInterface;

final class ReservationHandlerFactory
{
    public function __invoke(ContainerInterface $container): ReservationHandler
    {
        return new ReservationHandler(
            $container->get(AppointmentServiceInterface::class),
            $container->get(ReservationServiceInterface::class)
        );
    }
}

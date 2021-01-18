<?php

declare(strict_types=1);

namespace App\Handler\Appointment;

use App\Service\AppointmentServiceInterface;
use Interop\Container\ContainerInterface;

final class GetTimesHandlerFactory
{
    public function __invoke(ContainerInterface $container): GetTimesHandler
    {
        return new GetTimesHandler(
            $container->get(AppointmentServiceInterface::class)
        );
    }
}

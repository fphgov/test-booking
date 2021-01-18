<?php

declare(strict_types=1);

namespace App\Handler\Appointment;

use App\Service\AppointmentServiceInterface;
use Interop\Container\ContainerInterface;

final class GenerateHandlerFactory
{
    public function __invoke(ContainerInterface $container): GenerateHandler
    {
        return new GenerateHandler(
            $container->get(AppointmentServiceInterface::class)
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Handler\Information;

use App\Service\AppointmentServiceInterface;
use Interop\Container\ContainerInterface;

final class GetHandlerFactory
{
    public function __invoke(ContainerInterface $container): GetHandler
    {
        return new GetHandler(
            $container->get(AppointmentServiceInterface::class),
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Handler\Dashboard;

use App\Service\ApplicantServiceInterface;
use App\Service\AppointmentServiceInterface;
use App\Service\SettingServiceInterface;
use Interop\Container\ContainerInterface;

final class GetHandlerFactory
{
    public function __invoke(ContainerInterface $container): GetHandler
    {
        return new GetHandler(
            $container->get(ApplicantServiceInterface::class),
            $container->get(AppointmentServiceInterface::class),
            $container->get(SettingServiceInterface::class)
        );
    }
}

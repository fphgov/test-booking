<?php

declare(strict_types=1);

namespace App\Handler\Dashboard;

use App\Service\SettingServiceInterface;
use Interop\Container\ContainerInterface;

final class ChangeHandlerFactory
{
    public function __invoke(ContainerInterface $container): ChangeHandler
    {
        return new ChangeHandler(
            $container->get(SettingServiceInterface::class)
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Handler\Setting;

use Doctrine\ORM\EntityManagerInterface;
use Interop\Container\ContainerInterface;

final class GetHandlerFactory
{
    public function __invoke(ContainerInterface $container): GetHandler
    {
        return new GetHandler(
            $container->get(EntityManagerInterface::class)
        );
    }
}

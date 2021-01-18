<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Interop\Container\ContainerInterface;

final class PlaceServiceFactory
{
    /**
     * @return PlaceService
     */
    public function __invoke(ContainerInterface $container)
    {
        return new PlaceService(
            $container->get(EntityManagerInterface::class)
        );
    }
}

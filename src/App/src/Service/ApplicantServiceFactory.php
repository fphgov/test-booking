<?php

declare(strict_types=1);

namespace App\Service;

use App\Middleware\AuditMiddleware;
use Doctrine\ORM\EntityManagerInterface;
use Interop\Container\ContainerInterface;

final class ApplicantServiceFactory
{
    /**
     * @return ApplicantService
     */
    public function __invoke(ContainerInterface $container)
    {
        return new ApplicantService(
            $container->get(EntityManagerInterface::class),
            $container->get(AuditMiddleware::class)->getLogger()
        );
    }
}

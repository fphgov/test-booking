<?php

declare(strict_types=1);

namespace App\Handler\Applicant;

use App\Service\ApplicantServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Interop\Container\ContainerInterface;

final class CheckPostHandlerFactory
{
    public function __invoke(ContainerInterface $container): CheckPostHandler
    {
        return new CheckPostHandler(
            $container->get(EntityManagerInterface::class),
            $container->get(ApplicantServiceInterface::class)
        );
    }
}

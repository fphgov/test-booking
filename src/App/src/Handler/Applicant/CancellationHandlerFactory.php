<?php

declare(strict_types=1);

namespace App\Handler\Applicant;

use App\Service\ApplicantServiceInterface;
use Interop\Container\ContainerInterface;

final class CancellationHandlerFactory
{
    public function __invoke(ContainerInterface $container): CancellationHandler
    {
        return new CancellationHandler(
            $container->get(ApplicantServiceInterface::class)
        );
    }
}

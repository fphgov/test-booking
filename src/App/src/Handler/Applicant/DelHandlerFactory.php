<?php

declare(strict_types=1);

namespace App\Handler\Applicant;

use App\Service\ApplicantServiceInterface;
use Interop\Container\ContainerInterface;

final class DelHandlerFactory
{
    public function __invoke(ContainerInterface $container): DelHandler
    {
        return new DelHandler(
            $container->get(ApplicantServiceInterface::class)
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Handler\Applicant;

use App\Service\ApplicantServiceInterface;
use Interop\Container\ContainerInterface;

final class CheckGetHandlerFactory
{
    public function __invoke(ContainerInterface $container): CheckGetHandler
    {
        return new CheckGetHandler(
            $container->get(ApplicantServiceInterface::class)
        );
    }
}

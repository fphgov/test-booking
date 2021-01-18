<?php

declare(strict_types=1);

namespace App\Handler\Applicant;

use App\Service\ApplicantServiceInterface;
use Interop\Container\ContainerInterface;

final class ExportHandlerFactory
{
    public function __invoke(ContainerInterface $container): ExportHandler
    {
        return new ExportHandler(
            $container->get(ApplicantServiceInterface::class)
        );
    }
}

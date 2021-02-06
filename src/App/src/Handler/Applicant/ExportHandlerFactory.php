<?php

declare(strict_types=1);

namespace App\Handler\Applicant;

use App\Model\ApplicantExportModel;
use Interop\Container\ContainerInterface;

final class ExportHandlerFactory
{
    public function __invoke(ContainerInterface $container): ExportHandler
    {
        return new ExportHandler(
            $container->get(ApplicantExportModel::class)
        );
    }
}

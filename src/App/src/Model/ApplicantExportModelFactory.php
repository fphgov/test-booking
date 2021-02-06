<?php

declare(strict_types=1);

namespace App\Model;

use App\Service\ApplicantServiceInterface;
use Interop\Container\ContainerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

final class ApplicantExportModelFactory
{
    /**
     * @return ApplicantExportModel
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->has('config') ? $container->get('config') : [];

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator("Test-booking")
            ->setLastModifiedBy("Test-booking")
            ->setTitle("Export")
            ->setSubject("Export")
            ->setDescription("Export")
        ;

        return new ApplicantExportModel(
            $spreadsheet,
            $container->get(ApplicantServiceInterface::class)
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Model;

use App\Service\ApplicantServiceInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\IWriter;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

final class ApplicantExportModel implements ExportModelInterface
{
    public const HEADER = [
        'Azonosító',
        'Családnév',
        'Utónév',
        'Telefon',
        'E-mail',
        'Időpont',
    ];

    private Spreadsheet $spreadsheet;
    private ApplicantServiceInterface $applicantService;

    public function __construct(
        Spreadsheet $spreadsheet,
        ApplicantServiceInterface $applicantService
    ) {
        $this->spreadsheet      = $spreadsheet;
        $this->applicantService = $applicantService;
    }

    public function getWriter(): IWriter
    {
        $applicantRepository = $this->applicantService->getRepository();

        $applicantList = $applicantRepository->findBy([], [
            'appointment' => 'ASC',
        ]);

        $data = [];
        foreach ($applicantList as $app) {
            $sep = $app->getAppointment()->getDate()->format('Y-m-d') . ' ' . $app->getAppointment()->getPlace()->getName();

            if (! isset($data[$sep])) {
                $data[$sep][] = self::HEADER;
            }

            $data[$sep][] = [
                $app->getHumanId(),
                $app->getLastname(),
                $app->getFirstname(),
                $app->getPhone(),
                $app->getEmail(),
                $app->getAppointment()->getDate()->format('Y-m-d H:i'),
            ];
        }

        foreach ($data as $sep => $apps) {
            $sheet = $this->spreadsheet->createSheet();
            $sheet->setTitle($sep);
            $sheet->fromArray($apps, null, 'A1');

            foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        $this->spreadsheet->removeSheetByIndex(0);

        return new Xlsx($this->spreadsheet);
    }
}

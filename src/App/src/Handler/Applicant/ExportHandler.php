<?php

declare(strict_types=1);

namespace App\Handler\Applicant;

use App\Model\ApplicantExportModel;
use DateTime;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function header;
use function ob_end_clean;
use function ob_get_length;

final class ExportHandler implements RequestHandlerInterface
{
    /** @var ApplicantExportModel */
    private $applicantExportModel;

    public function __construct(
        ApplicantExportModel $applicantExportModel
    ) {
        $this->applicantExportModel = $applicantExportModel;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $date = (new DateTime())->getTimestamp();

        $writer = $this->applicantExportModel->getWriter();

        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=\"export-$date.xlsx\"");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Description: File Transfer");
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate");
        header("Content-Length: " . ob_get_length());

        if (ob_get_length()) {
            ob_end_clean();
        }

        $writer->save('php://output');

        exit();
    }
}

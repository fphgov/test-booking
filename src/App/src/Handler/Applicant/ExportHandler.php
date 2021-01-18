<?php

declare(strict_types=1);

namespace App\Handler\Applicant;

use App\Service\ApplicantServiceInterface;
use DateTime;
use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function implode;
use function mb_convert_encoding;

final class ExportHandler implements RequestHandlerInterface
{
    /** @var ApplicantServiceInterface */
    private $applicantService;

    public function __construct(
        ApplicantServiceInterface $applicantService
    ) {
        $this->applicantService = $applicantService;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $applicantRepository = $this->applicantService->getRepository();

        $appList = $applicantRepository->findBy([], [
            'appointment' => 'ASC',
        ]);

        $applicants = [];
        foreach ($appList as $app) {
            $applicants[] = implode(';', [
                $app->getHumanId(),
                $app->getLastname(),
                $app->getFirstname(),
                $app->getPhone(),
                $app->getAppointment()->getDate()->format('Y-m-d H:i'),
                $app->getEmail(),
            ]);
        }

        $date = (new DateTime())->getTimestamp();

        $csv = mb_convert_encoding(implode("\n", $applicants), 'UTF-16LE', 'UTF-8');

        return new TextResponse($csv, 200, [
            'Content-Type'              => 'text/csv',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition'       => "attachment; filename=export-'$date'.csv",
        ]);
    }
}

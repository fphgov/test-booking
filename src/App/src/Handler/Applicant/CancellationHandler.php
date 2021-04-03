<?php

declare(strict_types=1);

namespace App\Handler\Applicant;

use App\Service\ApplicantServiceInterface;
use App\Exception\ApplicantAlreadyParticipatedException;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class CancellationHandler implements RequestHandlerInterface
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

        $body = $request->getParsedBody();

        $cancelHash = $body['cancelHash'] ? $body['cancelHash'] : null;

        $applicant = $applicantRepository->findOneBy([
            'cancelHash' => $cancelHash,
        ]);

        if (! $applicant || $cancelHash === null) {
            return new JsonResponse([
                'errors' => [
                    'applicant' => ['Nem található a jelentkezés.'],
                ],
            ], 404);
        }

        $deleted = $this->applicantService->removeApplication($applicant);

        try {
            $this->applicantService->removeApplication($applicant);
        } catch (ApplicantAlreadyParticipatedException $th) {
            return new JsonResponse([
                'errors' => [
                    'applicant' => ['A jelentkezéssel már részt vettek az eseményen, ezért törlésre már nincs lehetőség.'],
                ],
            ], 500);
        } catch (\Throwable $th) {
            return new JsonResponse([
                'errors' => [
                    'applicant' => ['Hiba történt a jelentkezés törlése közben.'],
                ],
            ], 500);
        }

        return new JsonResponse([
            'data' => null,
        ]);
    }
}

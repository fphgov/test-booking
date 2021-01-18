<?php

declare(strict_types=1);

namespace App\Handler\Applicant;

use App\Service\ApplicantServiceInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class DelHandler implements RequestHandlerInterface
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

        $routeResult = $request->getAttribute(RouteResult::class);
        $applicantId = (int) $routeResult->getMatchedParams()['id'];

        $applicant = $applicantRepository->find($applicantId);

        if (! $applicant) {
            return new JsonResponse([
                'errors' => [
                    'applicant' => ['Not found applicant.'],
                ],
            ], 404);
        }

        $deleted = $this->applicantService->removeApplication($applicant);

        if (! $deleted) {
            return new JsonResponse([
                'errors' => [
                    'applicant' => ['An error occurred while deleting the applicant.'],
                ],
            ], 500);
        }

        return new JsonResponse([
            'data' => null,
        ]);
    }
}

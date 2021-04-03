<?php

declare(strict_types=1);

namespace App\Handler\Applicant;

use App\Exception\ApplicantAlreadyParticipatedException;
use App\Service\ApplicantServiceInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

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

        try {
            $this->applicantService->removeApplication($applicant);
        } catch (ApplicantAlreadyParticipatedException $th) {
            return new JsonResponse([
                'errors' => [
                    'applicant' => ['A jelentkezéssel már részt vettek az eseményen, ezért törlésre már nincs lehetőség.'],
                ],
            ], 500);
        } catch (Throwable $th) {
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

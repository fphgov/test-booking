<?php

declare(strict_types=1);

namespace App\Handler\Applicant;

use App\Entity\Applicant;
use App\Model\ApplicantCheckModel;
use App\Service\ApplicantServiceInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class CheckGetHandler implements RequestHandlerInterface
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
        $humanId     = $routeResult->getMatchedParams()['humanId'];

        $applicant = $applicantRepository->findOneBy([
            'humanId' => $humanId,
        ]);

        $applicantCheckModel = new ApplicantCheckModel();

        $applicantData = [];

        if ($applicant instanceof Applicant) {
            $applicantData = $applicantCheckModel->parseModel($applicant);
        }

        return new JsonResponse([
            'data' => $applicantData,
        ]);
    }
}

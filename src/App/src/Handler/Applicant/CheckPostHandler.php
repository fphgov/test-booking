<?php

declare(strict_types=1);

namespace App\Handler\Applicant;

use App\Model\ApplicantCheckModel;
use App\Service\ApplicantServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class CheckPostHandler implements RequestHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var ApplicantServiceInterface */
    private $applicantService;

    public function __construct(
        EntityManagerInterface $em,
        ApplicantServiceInterface $applicantService
    ) {
        $this->em               = $em;
        $this->applicantService = $applicantService;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();

        $applicantRepository = $this->applicantService->getRepository();

        $routeResult = $request->getAttribute(RouteResult::class);
        $humanId     = $routeResult->getMatchedParams()['humanId'];

        $applicant = $applicantRepository->findOneBy([
            'humanId' => $humanId,
        ]);

        if (! $applicant) {
            return new JsonResponse([
                'data' => [
                    'unsuccess' => 'No result',
                ],
            ], 404);
        }

        if (! empty($body['attended'])) {
            $applicant->setAttended(! ($body['attended'] === "true" || $body['attended'] === true));
        }

        $this->em->flush();

        $applicantCheckModel = new ApplicantCheckModel();

        return new JsonResponse([
            'data' => $applicantCheckModel->parseModel($applicant),
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Handler\Appointment;

use App\Service\AppointmentServiceInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function getenv;

final class GetTimesHandler implements RequestHandlerInterface
{
    /** @var AppointmentServiceInterface */
    private $appointmentService;

    public function __construct(
        AppointmentServiceInterface $appointmentService
    ) {
        $this->appointmentService = $appointmentService;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $appointmentRepository = $this->appointmentService->getRepository();
        $routeResult           = $request->getAttribute(RouteResult::class);
        $placeId               = (int) $routeResult->getMatchedParams()['id'];

        $entities = $appointmentRepository->belongingToPlaceTime($placeId, (int) getenv('APP_PHASE'));

        return new JsonResponse([
            'data' => $entities,
        ]);
    }
}

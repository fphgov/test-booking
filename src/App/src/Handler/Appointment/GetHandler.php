<?php

declare(strict_types=1);

namespace App\Handler\Appointment;

use App\Service\AppointmentServiceInterface;
use DateTime;
use Exception;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function getenv;

final class GetHandler implements RequestHandlerInterface
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
        $filterDate            = $routeResult->getMatchedParams()['date'];

        $date = null;
        try {
            $date = DateTime::createFromFormat('Y-m-d', $filterDate);
        } catch (Exception $e) {
        }

        $entities = $appointmentRepository->belongingToPlace($placeId, $date, (int) getenv('APP_PHASE'));

        return new JsonResponse([
            'data' => $entities,
        ]);
    }
}

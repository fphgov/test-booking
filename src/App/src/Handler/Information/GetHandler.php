<?php

declare(strict_types=1);

namespace App\Handler\Information;

use App\Service\AppointmentServiceInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

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

        $appointments = $appointmentRepository->getAppointmentsForInformation();

        return new JsonResponse([
            'infos' => [
                'appointments' => $appointments,
            ],
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Handler\Appointment;

use App\Service\AppointmentServiceInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class GenerateHandler implements RequestHandlerInterface
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
        $this->appointmentService->generateEmptyEntities();

        return new JsonResponse([
            'data' => 'success',
        ]);
    }
}

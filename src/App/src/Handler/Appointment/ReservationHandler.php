<?php

declare(strict_types=1);

namespace App\Handler\Appointment;

use App\Exception as Except;
use App\Service\AppointmentServiceInterface;
use App\Service\ReservationServiceInterface;
use Exception;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ReservationHandler implements RequestHandlerInterface
{
    /** @var AppointmentServiceInterface */
    private $appointmentService;

    /** @var ReservationServiceInterface */
    private $reservationService;

    public function __construct(
        AppointmentServiceInterface $appointmentService,
        ReservationServiceInterface $reservationService
    ) {
        $this->appointmentService = $appointmentService;
        $this->reservationService = $reservationService;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body                  = $request->getParsedBody();
        $appointmentRepository = $this->appointmentService->getRepository();

        $appointmentId = $body['appointment'] ? $body['appointment'] : null;
        $sessionId     = $body['sessionId'] ? $body['sessionId'] : null;

        if ($appointmentId === null || $sessionId === null) {
            return new JsonResponse([
                'errors' => [
                    'appointment' => [
                        'reservation' => 'Nem elérhető az kiválasztott időpont.',
                    ],
                ],
            ], 422);
        }

        $expiry = null;
        try {
            $appointment = $appointmentRepository->find($appointmentId);

            if (! $appointment) {
                throw new Except\AppointmentNoAvailableException('No available appointment');
            }

            $expiry = $this->reservationService->reserv($appointment, $sessionId);
        } catch (Except\AppointmentNoAvailableException $e) {
            return new JsonResponse([
                'errors' => [
                    'appointment' => [
                        'reservation' => 'Nem elérhető az kiválasztott időpont.',
                    ],
                ],
            ], 422);
        } catch (Exception $e) {
            return new JsonResponse([
                'errors' => [
                    'appointment' => [
                        'reservation' => 'Időközben lefoglalták már időpontot, válasszon másikat.',
                    ],
                ],
            ], 422);
        }

        return new JsonResponse([
            'exp' => $expiry,
        ]);
    }
}

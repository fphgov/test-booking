<?php

declare(strict_types=1);

namespace App\Handler\Appointment;

use App\Generator\AppointmentGeneratorOptions;
use App\Service\AppointmentServiceInterface;
use Exception;
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
        $body = $request->getParsedBody();

        if (! isset($body['options'])) {
            return new JsonResponse([
                'errors' => [
                    'options' => 'Missing options parameter',
                ],
            ], 422);
        }

        $appGenOptions = new AppointmentGeneratorOptions();
        $appGenOptions->parseFromArray($body['options']);

        try {
            $this->appointmentService->generateEmptyEntities($appGenOptions);
        } catch (Exception $e) {
            return new JsonResponse([
                'errors' => [
                    'generator' => 'A server error has occurred',
                ],
            ], 500);
        }

        return new JsonResponse([
            'data' => 'success',
        ]);
    }
}

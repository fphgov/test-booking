<?php

declare(strict_types=1);

namespace App\Handler\Dashboard;

use App\Service\ApplicantServiceInterface;
use App\Service\AppointmentServiceInterface;
use App\Service\SettingServiceInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function getenv;

final class GetHandler implements RequestHandlerInterface
{
    /** @var ApplicantServiceInterface */
    private $applicantService;

    /** @var AppointmentServiceInterface */
    private $appointmentService;

    /** @var SettingServiceInterface **/
    private $settingService;

    public function __construct(
        ApplicantServiceInterface $applicantService,
        AppointmentServiceInterface $appointmentService,
        SettingServiceInterface $settingService
    ) {
        $this->applicantService   = $applicantService;
        $this->appointmentService = $appointmentService;
        $this->settingService     = $settingService;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $setting = $this->settingService->getRepository()->find(1);

        $applicantRepository   = $this->applicantService->getRepository();
        $appointmentRepository = $this->appointmentService->getRepository();

        $reserved  = $applicantRepository->getCount();
        $available = $appointmentRepository->getAvailableAppointments((int) getenv('APP_PHASE'));
        $banned    = $appointmentRepository->getBannedAppointments((int) getenv('APP_PHASE'));

        return new JsonResponse([
            'settings' => $setting,
            'infos'    => [
                'reserved'  => $reserved,
                'available' => $available,
                'banned'    => $banned,
            ],
        ]);
    }
}

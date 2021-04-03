<?php

declare(strict_types=1);

namespace App\Handler\Applicant;

use App\Middleware\AuditMiddleware;
use App\Service\ApplicantServiceInterface;
use App\Service\EncryptServiceInterface;
use App\Service\SettingServiceInterface;
use Exception;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Mail\Header\HeaderName;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function strtolower;

final class AddHandler implements RequestHandlerInterface
{
    /** @var AuditMiddleware */
    protected $audit;

    /** @var InputFilterInterface */
    private $inputFilter;

    /** @var ApplicantServiceInterface */
    private $applicantService;

    /** @var SettingServiceInterface */
    private $settingService;

    /** @var EncryptServiceInterface */
    private $encryptService;

    public function __construct(
        InputFilterInterface $inputFilter,
        ApplicantServiceInterface $applicantService,
        SettingServiceInterface $settingService,
        EncryptServiceInterface $encryptService
    ) {
        $this->inputFilter      = $inputFilter;
        $this->applicantService = $applicantService;
        $this->settingService   = $settingService;
        $this->encryptService   = $encryptService;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->audit = $request->getAttribute(AuditMiddleware::class);

        $body = $request->getParsedBody();

        $sessionId = $body['sessionId'] ? (string) $body['sessionId'] : null;

        if ($sessionId === null) {
            return new JsonResponse([
                'errors' => [
                    'session' => 'Hiányzik a munkamenet azonosító',
                ],
            ], 422);
        }

        if (isset($body['rcptch']) && ! empty($body['rcptch'])) {
            return new JsonResponse([
                'success' => true,
            ]);
        }

        $setting = $this->settingService->getRepository()->find(1);

        if ($setting && $setting->getClose()) {
            return new JsonResponse([
                'errors' => [
                    'final' => 'A regisztráció már lezárult',
                ],
            ], 422);
        }

        $this->inputFilter->setData($body);

        if (! $this->inputFilter->isValid()) {
            return new JsonResponse([
                'errors' => $this->inputFilter->getMessages(),
            ], 422);
        }

        $email = strtolower($this->inputFilter->getValues()['email']);

        try {
            HeaderName::assertValid($email);
        } catch (Exception $e) {
            return new JsonResponse([
                'errors' => [
                    'email' => [
                        'format' => 'Nem megfelelő e-mail cím. Kérjük ellenőrizze újra. Ékezetes betűk és a legtöbb speciális karakter nem elfogadható.',
                    ],
                ],
            ], 422);
        }

        $taj = $this->inputFilter->getValues()['taj'];

        $existsTaj = $this->applicantService->getRepository()->findOneBy([
            'taj' => $this->encryptService->encrypt($taj),
        ]);

        if ($existsTaj) {
            return new JsonResponse([
                'errors' => [
                    'taj' => [
                        'exists' => 'Ezzel a TAJ-számmal már történt regisztráció',
                    ],
                ],
            ], 422);
        }

        try {
            $applicant = $this->applicantService->addApplicant($this->inputFilter->getValues(), $sessionId);
        } catch (Exception $e) {
            if ($e->getCode() === 0 || $e->getCode() === 422) {
                $this->audit->err('They tried to register for an already booked place');

                return new JsonResponse([
                    'errors' => [
                        'appointment' => [
                            'reserved' => 'Ez az időpontot már foglalt, frissítettük az időpont listát a kiválaszott naphoz. Kérjük válasszon az elérhetők közül.',
                        ],
                    ],
                ], 422);
            }

            return new JsonResponse([
                'errors' => $e->getMessage(),
                'code'   => $e->getCode(),
            ], 500);
        }

        return new JsonResponse([
            'data' => [
                'humanId' => $applicant->getHumanId(),
                'place'   => $applicant->getAppointment()->getPlace()->getDescription(),
                'date'    => $applicant->getAppointment()->getDate()->format('Y-m-d H.i'),
            ],
        ]);
    }
}

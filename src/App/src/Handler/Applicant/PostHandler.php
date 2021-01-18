<?php

declare(strict_types=1);

namespace App\Handler\Applicant;

use App\Service\ApplicantServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Mail\Header\HeaderName;
use Laminas\Validator;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class PostHandler implements RequestHandlerInterface
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
        $applicantId = (int) $routeResult->getMatchedParams()['id'];

        $applicant = $applicantRepository->find($applicantId);

        if (! $applicant) {
            return new JsonResponse([
                'data' => [
                    'unsuccess' => 'No result',
                ],
            ], 404);
        }

        if (! empty($body['firstname'])) {
            $applicant->setFirstname($body['firstname']);
        }

        if (! empty($body['lastname'])) {
            $applicant->setLastname($body['lastname']);
        }

        if (! empty($body['email'])) {
            $emailValidator = new Validator\EmailAddress([
                'messages' => [
                    Validator\EmailAddress::INVALID            => "Érvénytelen típus megadva. Szöveg adható meg.",
                    Validator\EmailAddress::INVALID_FORMAT     => "A bevitel nem érvényes e-mail cím. Használja az alapformátumot pl. email@kiszolgalo.hu",
                    Validator\EmailAddress::INVALID_HOSTNAME   => "'%hostname%' érvénytelen gazdagépnév",
                    Validator\EmailAddress::INVALID_MX_RECORD  => "'%hostname%' úgy tűnik, hogy az e-mail címhez nincs érvényes MX vagy A rekordja",
                    Validator\EmailAddress::INVALID_SEGMENT    => "'%hostname%' is not in a routable network segment. The email address should not be resolved from public network",
                    Validator\EmailAddress::DOT_ATOM           => "'%localPart%' can not be matched against dot-atom format",
                    Validator\EmailAddress::QUOTED_STRING      => "'%localPart%' nem illeszthető idézőjel a szövegbe",
                    Validator\EmailAddress::INVALID_LOCAL_PART => "'%localPart%' nem érvényes az e-mail cím helyi része",
                    Validator\EmailAddress::LENGTH_EXCEEDED    => "A szöveg meghaladja az engedélyezett hosszúságot",
                ],
            ]);

            if (! $emailValidator->isValid($body['email'])) {
                return new JsonResponse([
                    'errors' => [
                        'email' => $emailValidator->getMessages(),
                    ],
                ], 422);
            }

            try {
                HeaderName::assertValid($body['email']);
            } catch (Exception $e) {
                return new JsonResponse([
                    'errors' => [
                        'email' => [
                            'format' => 'Nem megfelelő e-mail cím. Kérjük ellenőrizze újra. Ékezetes betűk és a legtöbb speciális karakter nem elfogadható.',
                        ],
                    ],
                ], 422);
            }

            $applicant->setEmail($body['email']);
        }

        if (! empty($body['phone'])) {
            $applicant->setPhone($body['phone']);
        }

        if (! empty($body['reNotified'])) {
            $applicant->setNotified((bool) $body['reNotified']);
        }

        $this->em->flush();

        return new JsonResponse([
            'data' => $applicant,
            're'   => $body['reNotified'],
        ]);
    }
}

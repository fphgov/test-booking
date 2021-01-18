<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Applicant;
use App\Entity\ApplicantInterface;
use App\Entity\Appointment;
use App\Entity\Reservation;
use App\Repository\ApplicantRepositoryInterface;
use App\Repository\AppointmentRepositoryInterface;
use App\Repository\ReservationRepositoryInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Laminas\Log\Logger;

use function bin2hex;
use function random_bytes;
use function random_int;

final class ApplicantService implements ApplicantServiceInterface
{
    /** @var ApplicantRepositoryInterface */
    protected $applicantRepository;

    /** @var AppointmentRepositoryInterface */
    protected $appointmentRepository;

    /** @var ReservationRepositoryInterface */
    protected $reservationRepository;

    /** @var EntityManagerInterface */
    protected $em;

    /** @var Logger */
    private $audit;

    public function __construct(
        EntityManagerInterface $em,
        Logger $audit
    ) {
        $this->em                    = $em;
        $this->applicantRepository   = $this->em->getRepository(Applicant::class);
        $this->reservationRepository = $this->em->getRepository(Reservation::class);
        $this->appointmentRepository = $this->em->getRepository(Appointment::class);
        $this->audit                 = $audit;
    }

    public function addApplicant(array $filteredParams, string $sessionId): ApplicantInterface
    {
        $date = new DateTime();

        $applicant = new Applicant();

        $appointment = $this->appointmentRepository->find($filteredParams['appointment']);

        if (! $appointment || ! $appointment->getAvailable()) {
            throw new Exception("Appointment reserved", 422);
        }

        $reservation = $this->reservationRepository->findOneBy([
            'appointment' => $appointment->getId(),
        ]);

        if ($reservation && $reservation->getSession() !== $sessionId) {
            throw new Exception("Appointment reserved", 422);
        }

        $appointment->setActive(false);

        $applicant->setAppointment($appointment);
        $applicant->setCancelHash($this->generatCancelHash());
        $applicant->setHumanId($this->generateHumanId($appointment->getPlace()->getShortName()));
        $applicant->setPrivacy((bool) $filteredParams['privacy']);
        $applicant->setFirstname($filteredParams['firstname']);
        $applicant->setLastname($filteredParams['lastname']);
        $applicant->setEmail($filteredParams['email']);
        $applicant->setPhone($filteredParams['phone']);
        $applicant->setAddress($filteredParams['address']);
        $applicant->setBirthdayPlace($filteredParams['birthdayPlace']);
        $applicant->setBirthday(DateTime::createFromFormat('Y.m.d', $filteredParams['birthday']));
        $applicant->setTaj($filteredParams['taj']);
        $applicant->setActive(true);
        $applicant->setCreatedAt($date);
        $applicant->setUpdatedAt($date);

        $this->em->persist($applicant);
        $this->em->flush();

        return $applicant;
    }

    public function removeApplication(ApplicantInterface $applicant): bool
    {
        $appointment          = $applicant->getAppointment();
        $date                 = $appointment->getDate();
        $availableAppointment = $date->format('Y-m-d') > (new DateTime('now'))->format('Y-m-d');

        if (! $availableAppointment) {
            $appointment->setBanned(true);
        }

        try {
            $appointment->setActive(true);

            $this->em->remove($applicant);
            $this->em->flush();

            return true;
        } catch (Exception $e) {
            $this->audit->err('Applicant could not be deleted', [
                'extra' => $applicant->getHumanId() . ' (' . $appointment->getId() . ')',
            ]);
        }

        return false;
    }

    public function getRepository(): ApplicantRepositoryInterface
    {
        return $this->applicantRepository;
    }

    private function generateHumanId(string $prefix = ''): string
    {
        $humanId = random_int(10000, 99999);

        $existsHumanId = null;
        do {
            $existsHumanId = $this->applicantRepository->findOneBy([
                'humanId' => $prefix ? $prefix . '-' . $humanId : $humanId,
            ]);
        } while ($existsHumanId !== null);

        return (string) ($prefix ? $prefix . '-' . $humanId : $humanId);
    }

    private function generatCancelHash(): string
    {
        $cancelHash = bin2hex(random_bytes(8));

        $existsHumanId = null;
        do {
            $existsHumanId = $this->applicantRepository->findOneBy([
                'cancelHash' => $cancelHash,
            ]);
        } while ($existsHumanId !== null);

        return $cancelHash;
    }
}

<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Appointment;
use App\Entity\AppointmentInterface;
use App\Entity\Reservation;
use App\Exception\AppointmentNoAvailableException;
use App\Repository\AppointmentRepositoryInterface;
use App\Repository\ReservationRepositoryInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

final class ReservationService implements ReservationServiceInterface
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var ReservationRepositoryInterface */
    protected $reservationRepository;

    /** @var AppointmentRepositoryInterface */
    protected $appointmentRepository;

    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em                    = $em;
        $this->reservationRepository = $this->em->getRepository(Reservation::class);
        $this->appointmentRepository = $this->em->getRepository(Appointment::class);
    }

    public function getRepository(): ReservationRepositoryInterface
    {
        return $this->reservationRepository;
    }

    public function reserv(AppointmentInterface $appointment, string $sessionId): ?DateTime
    {
        if (! $appointment->getAvailable()) {
            throw new AppointmentNoAvailableException('No available appointment');
        }

        $reservation = $this->reservationRepository->findOneBy([
            'session' => $sessionId,
        ]);

        if ($reservation && $reservation->getAppointment()->getId() === $appointment->getId()) {
            $reservation->setExpiry($this->getExpirationTime());

            $this->em->flush();

            return $reservation->getExpiry();
        } elseif ($reservation && $reservation->getAppointment()->getId() !== $appointment->getId()) {
            $this->em->remove($reservation);

            $this->em->flush();
        }

        $reservation = $this->reservationRepository->findOneBy([
            'appointment' => $appointment,
        ]);

        if (! $reservation) {
            $reservation = new Reservation();
            $reservation->setAppointment($appointment);
            $reservation->setExpiry($this->getExpirationTime());
            $reservation->setSession($sessionId);

            $this->em->persist($reservation);
            $this->em->flush();

            return $reservation->getExpiry();
        }

        throw new AppointmentNoAvailableException('No available appointment');
    }

    public function clearExpiredData(): void
    {
        $reservations = $this->reservationRepository->findAll();

        foreach ($reservations as $reservation) {
            if ($reservation->getExpiry() <= new DateTime()) {
                $this->em->remove($reservation);
            }
        }

        $this->em->flush();
    }

    private function getExpirationTime(): DateTime
    {
        $time = new DateTime();
        $time->modify("+" . self::SESSION_ALIVE_TIME . " minutes");

        return $time;
    }
}

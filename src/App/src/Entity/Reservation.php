<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\AppointmentInterface;
use App\Traits\EntityTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReservationRepository")
 * @ORM\Table(name="reservations")
 */
class Reservation implements JsonSerializable, ReservationInterface
{
    use EntityTrait;

    public const DISABLE_SHOW_DEFAULT = [
        'updatedAt',
    ];

    /**
     * @ORM\Id
     * @ORM\Column(name="session", type="string", length=40, unique=true)
     *
     * @var string
     */
    private $session;

    /**
     * @ORM\ManyToOne(targetEntity="AppointmentInterface")
     * @ORM\JoinColumn(name="appointmentId", referencedColumnName="id", unique=true, nullable=false)
     *
     * @var AppointmentInterface
     */
    private $appointment;

    /**
     * @ORM\Column(name="expiry", type="datetime")
     *
     * @var DateTime
     */
    private $expiry;

    public function getSession(): string
    {
        return $this->session;
    }

    public function setSession(string $session): void
    {
        $this->session = $session;
    }

    public function getAppointment(): AppointmentInterface
    {
        return $this->appointment;
    }

    public function setAppointment(AppointmentInterface $appointment): void
    {
        $this->appointment = $appointment;
    }

    public function getExpiry(): DateTime
    {
        return $this->expiry;
    }

    public function setExpiry(DateTime $expiry): void
    {
        $this->expiry = $expiry;
    }
}

<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;

interface ReservationInterface
{
    public const DISABLE_DEFAULT_SET = [
        'id',
        'image',
        'password',
        'createdAt',
        'updatedAt',
    ];

    public function setProps(array $datas): void;

    public function getProps(): array;

    public function jsonSerialize(): array;

    public function toArray(): array;

    public function getSession(): string;

    public function setSession(string $session): void;

    public function getAppointment(): AppointmentInterface;

    public function setAppointment(AppointmentInterface $appointment): void;

    public function getExpiry(): DateTime;

    public function setExpiry(DateTime $expiry): void;
}

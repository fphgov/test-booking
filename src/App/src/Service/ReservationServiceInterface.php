<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\AppointmentInterface;
use App\Repository\ReservationRepositoryInterface;
use DateTime;

interface ReservationServiceInterface
{
    public const SESSION_ALIVE_TIME = 5;

    public function getRepository(): ReservationRepositoryInterface;

    public function reserv(AppointmentInterface $appointment, string $sessionId): ?DateTime;

    public function clearExpiredData(): void;
}

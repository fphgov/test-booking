<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AppointmentInterface;
use DateTime;
use Doctrine\Persistence\ObjectRepository;

interface AppointmentRepositoryInterface extends ObjectRepository
{
    /** @return AppointmentInterface[] */
    public function belongingToPlace(int $placeId, DateTime $date, int $phase = 0);

    /** @return AppointmentInterface[] */
    public function belongingToPlaceTime(int $placeId, int $phase = 0): array;

    /** @return mixed|int */
    public function getAvailableAppointments(int $phase = 0);

    /** @return mixed|int */
    public function getBannedAppointments(int $phase = 0);
}

<?php

declare(strict_types=1);

namespace App\Entity;

use App\Interfaces\EntityInterface;
use DateTime;

interface AppointmentInterface extends EntityInterface
{
    public function getActive(): bool;

    public function setActive(bool $active): void;

    public function setPlace(PlaceInterface $place): void;

    public function getPlace(): PlaceInterface;

    public function setDate(DateTime $date): void;

    public function getDate(): DateTime;

    public function setPhase(int $phase): void;

    public function getPhase(): int;

    public function setBanned(bool $banned): void;

    public function getBanned(): bool;

    public function getAvailable(): bool;
}

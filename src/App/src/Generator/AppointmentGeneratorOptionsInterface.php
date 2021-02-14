<?php

declare(strict_types=1);

namespace App\Generator;

use DateTime;

interface AppointmentGeneratorOptionsInterface
{
    public const DEFAULT_INTERVAL        = 10;
    public const DEFAULT_INTERVAL_MATRIX = [
        0  => 1,
        10 => 1,
        20 => 1,
        30 => 1,
        40 => 1,
        50 => 1,
    ];

    public function parseFromArray(array $options): void;

    public function getStartTime(): int;

    public function getEndTime(): int;

    public function setStartDateTime(DateTime $startDateTime): void;

    public function getStartDateTime(): DateTime;

    public function setEndDateTime(DateTime $endDateTime): void;

    public function getEndDateTime(): DateTime;

    public function setInterval(int $interval): void;

    public function getInterval(): int;

    public function setIntervalMatrix(array $intervalMatrix): void;

    public function getIntervalMatrix(): array;

    public function setNormalLunchTime(bool $normalLunchTime): void;

    public function getNormalLunchTime(): bool;

    public function hasDiffInterval(): bool;

    public function getMaxIntervalValues(): int;
}

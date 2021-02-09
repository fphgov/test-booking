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

    public function setStartTime(int $startTime): void;

    public function getStartTime(): int;

    public function setEndTime(int $endTime): void;

    public function getEndTime(): int;

    public function setStartDate(DateTime $startDate): void;

    public function getStartDate(): DateTime;

    public function setEndDate(DateTime $endDate): void;

    public function getEndDate(): DateTime;

    public function setInterval(int $interval): void;

    public function getInterval(): int;

    public function setIntervalMatrix(array $intervalMatrix): void;

    public function getIntervalMatrix(): array;

    public function setNormalLunchTime(bool $normalLunchTime): void;

    public function getNormalLunchTime(): bool;
}

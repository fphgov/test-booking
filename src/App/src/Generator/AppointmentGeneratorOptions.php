<?php

declare(strict_types=1);

namespace App\Generator;

use DateTime;

final class AppointmentGeneratorOptions implements AppointmentGeneratorOptionsInterface
{
    private array $intervalMatrix = self::DEFAULT_INTERVAL_MATRIX;
    private int $interval         = self::DEFAULT_INTERVAL;

    private bool $normalLunchTime;
    private DateTime $startDate;
    private DateTime $endDate;
    private int $startTime;
    private int $endTime;

    public function parseFromArray(array $options): void
    {
        if (isset($options['startTime'])) {
            $this->setStartTime((int) $options['startTime']);
        }

        if (isset($options['endTime'])) {
            $this->setEndTime((int) $options['endTime']);
        }

        if (isset($options['startDate'])) {
            $this->setStartDate(new DateTime($options['startDate']));
        }

        if (isset($options['endDate'])) {
            $this->setEndDate(new DateTime($options['endDate']));
        }

        if (isset($options['interval'])) {
            $this->setIntervalMatrix((array) $options['interval']);
        }
    }

    public function setStartTime(int $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function getStartTime(): int
    {
        return $this->startTime;
    }

    public function setEndTime(int $endTime): void
    {
        $this->endTime = $endTime;
    }

    public function getEndTime(): int
    {
        return $this->endTime;
    }

    public function setStartDate(DateTime $startDate): void
    {
        $this->startDate = $startDate->setTime($this->startTime, 0);
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function setEndDate(DateTime $endDate): void
    {
        $this->endDate = $endDate->setTime($this->endTime, 0);
    }

    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    public function setInterval(int $interval): void
    {
        $this->interval = $interval;
    }

    public function getInterval(): int
    {
        return $this->interval;
    }

    public function setIntervalMatrix(array $intervalMatrix): void
    {
        $this->intervalMatrix = $intervalMatrix;
    }

    public function getIntervalMatrix(): array
    {
        return $this->intervalMatrix;
    }

    public function setNormalLunchTime(bool $normalLunchTime): void
    {
        $this->normalLunchTime = $normalLunchTime;
    }

    public function getNormalLunchTime(): bool
    {
        return $this->normalLunchTime;
    }
}

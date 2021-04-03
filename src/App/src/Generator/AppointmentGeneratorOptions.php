<?php

declare(strict_types=1);

namespace App\Generator;

use DateTime;

final class AppointmentGeneratorOptions implements AppointmentGeneratorOptionsInterface
{
    private array $intervalMatrix = self::DEFAULT_INTERVAL_MATRIX;
    private int $interval         = self::DEFAULT_INTERVAL;

    private bool $normalLunchTime;
    private DateTime $startDateTime;
    private DateTime $endDateTime;
    private int $startTime;
    private int $endTime;

    public function parseFromArray(array $options): void
    {
        if (isset($options['startDateTime'])) {
            $this->setStartDateTime(new DateTime($options['startDateTime']));
        }

        if (isset($options['endDateTime'])) {
            $this->setEndDateTime(new DateTime($options['endDateTime']));
        }

        if (isset($options['interval'])) {
            $this->setIntervalMatrix((array) $options['interval']);
        }
    }

    public function getStartTime(): int
    {
        return $this->startTime;
    }

    public function getEndTime(): int
    {
        return $this->endTime;
    }

    public function setStartDateTime(DateTime $startDateTime): void
    {
        $this->startDateTime = $startDateTime;

        $this->startTime = (int) $this->startDateTime->format('H');
    }

    public function getStartDateTime(): DateTime
    {
        return $this->startDateTime;
    }

    public function setEndDateTime(DateTime $endDateTime): void
    {
        $this->endDateTime = $endDateTime;

        $this->endTime = (int) $this->endDateTime->format('H');
    }

    public function getEndDateTime(): DateTime
    {
        return $this->endDateTime;
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

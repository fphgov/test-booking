<?php

declare(strict_types=1);

namespace App\Generator;

use DateInterval;
use DateTime;

use function array_merge;
use function str_pad;

use const STR_PAD_LEFT;

final class AppointmentGenerator
{
    private AppointmentGeneratorOptionsInterface $options;

    public function getDates(AppointmentGeneratorOptionsInterface $options): array
    {
        $this->options = $options;

        $incrementDay  = (int) $this->options->getStartDateTime()->format('d');
        $diffDateCount = (int) $this->options->getStartDateTime()->diff($this->options->getEndDateTime())->format('%a');

        $appoints = [];
        for ($i = 0; $i <= $diffDateCount; $i++) {
            $incrementDate = clone $this->options->getStartDateTime();
            $incrementDate = $incrementDate->add(new DateInterval('P'. $i .'D'));

            $appoints[] = $this->generator($incrementDate, $incrementDay);

            $incrementDay++;
        }

        return array_merge(...$appoints);
    }

    private function generator($incrementDate, $incrementDay)
    {
        $incrementDateStation = clone $incrementDate;

        $dayList = [];
        while ($incrementDateStation < new DateTime($incrementDateStation->format('Y-m-') . str_pad((string) $incrementDay, 2, '0', STR_PAD_LEFT) . " " .  $this->options->getEndDateTime()->format('H:i'))) {
            if ($this->isLunchTime($incrementDateStation, $incrementDay)) {
                continue;
            }

            $intervalMatrix = $this->options->getIntervalMatrix();

            for ($i = 0; $i < $intervalMatrix[(int)$incrementDateStation->format('i')]; $i++) {
                $dayList[] = clone $incrementDateStation;
            }

            $incrementDateStation->add(new DateInterval('PT' .  $this->options->getInterval() . 'M'));
        }

        return $dayList;
    }

    private function isLunchTime($incrementDateStation, $incrementDay): bool
    {
        if ($this->options->getNormalLunchTime()) {
            if (
                ($incrementDateStation >= new DateTime($incrementDateStation->format('Y-m-') . str_pad((string) $incrementDay, 2, '0', STR_PAD_LEFT) . " 12:00")) &&
                ($incrementDateStation < new DateTime($incrementDateStation->format('Y-m-') . str_pad((string) $incrementDay, 2, '0', STR_PAD_LEFT) . " 13:00"))
            ) {
                $incrementDateStation->add(new DateInterval('PT' .  $this->options->getInterval() . 'M'));

                return true;
            }
        } else {
            if (
                ($incrementDateStation >= new DateTime($incrementDateStation->format('Y-m-') . str_pad((string) $incrementDay, 2, '0', STR_PAD_LEFT) . " 13:00")) &&
                ($incrementDateStation < new DateTime($incrementDateStation->format('Y-m-') . str_pad((string) $incrementDay, 2, '0', STR_PAD_LEFT) . " 14:00"))
            ) {
                $incrementDateStation->add(new DateInterval('PT' .  $this->options->getInterval() . 'M'));

                return true;
            }
        }

        return false;
    }
}

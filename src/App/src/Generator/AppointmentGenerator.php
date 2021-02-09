<?php

declare(strict_types=1);

namespace App\Generator;

use DateInterval;
use DateTime;

use function array_merge;
use function array_values;
use function max;
use function str_pad;

use const STR_PAD_LEFT;

final class AppointmentGenerator
{
    public function getDates(AppointmentGeneratorOptionsInterface $options): array
    {
        $incrementDay  = (int) $options->getStartDate()->format('d');
        $diffDateCount = (int) $options->getStartDate()->diff($options->getEndDate())->format('%a');

        $appoints      = [];
        $incrementDate = clone $options->getStartDate();

        $intervalMatrix = $options->getIntervalMatrix();

        $stationMax = max(array_values($intervalMatrix));

        for ($i = 0; $i <= $diffDateCount; $i++) {
            $intervalMax = [];
            for ($s = 0; $s < 60 / $options->getInterval(); $s++) {
                $key = $options->getInterval() * $s;

                $intervalMax[$key] = ($intervalMatrix[$key]) * ($options->getEndTime() - $options->getStartTime());
            }

            $incrementDateStation = clone $incrementDate;

            for ($station = 0; $station < $stationMax; $station++) {
                $incrementDateStation = clone $incrementDate;

                $stationsList = [];
                while ($incrementDateStation < new DateTime($incrementDateStation->format('Y-m-') . str_pad((string) $incrementDay, 2, '0', STR_PAD_LEFT) . " " . str_pad((string) $options->getEndTime(), 2, '0', STR_PAD_LEFT) . ":00")) {
                    if ($options->getNormalLunchTime()) {
                        if (
                            ($incrementDateStation >= new DateTime($incrementDateStation->format('Y-m-') . str_pad((string) $incrementDay, 2, '0', STR_PAD_LEFT) . " 12:00")) &&
                            ($incrementDateStation < new DateTime($incrementDateStation->format('Y-m-') . str_pad((string) $incrementDay, 2, '0', STR_PAD_LEFT) . " 13:00"))
                        ) {
                            $intervalMax[(int) $incrementDateStation->format('i')]--;

                            $incrementDateStation->add(new DateInterval('PT' . $options->getInterval() . 'M'));

                            continue;
                        }
                    } else {
                        if (
                            ($incrementDateStation >= new DateTime($incrementDateStation->format('Y-m-') . str_pad((string) $incrementDay, 2, '0', STR_PAD_LEFT) . " 13:00")) &&
                            ($incrementDateStation < new DateTime($incrementDateStation->format('Y-m-') . str_pad((string) $incrementDay, 2, '0', STR_PAD_LEFT) . " 14:00"))
                        ) {
                            $intervalMax[(int) $incrementDateStation->format('i')]--;

                            $incrementDateStation->add(new DateInterval('PT' . $options->getInterval() . 'M'));

                            continue;
                        }
                    }

                    if ($intervalMax[(int) $incrementDateStation->format('i')] <= 0) {
                        $incrementDateStation->add(new DateInterval('PT' . $options->getInterval() . 'M'));

                        continue;
                    }

                    $intervalMax[(int) $incrementDateStation->format('i')]--;
                    $stationsList[] = clone $incrementDateStation;

                    $incrementDateStation->add(new DateInterval('PT' . $options->getInterval() . 'M'));
                }

                $appoints = array_merge($appoints, $stationsList);
            }

            $incrementDay++;

            $incrementDate = clone new DateTime($incrementDateStation->format('Y-m-') . str_pad((string) $incrementDay, 2, '0', STR_PAD_LEFT) . " " . str_pad((string) $options->getStartTime(), 2, '0', STR_PAD_LEFT) . ":00");
        }

        return $appoints;
    }
}

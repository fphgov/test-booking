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
    public function getDates(AppointmentGeneratorOptionsInterface $options): array
    {
        $incrementDay  = (int) $options->getStartDateTime()->format('d');
        $diffDateCount = (int) $options->getStartDateTime()->diff($options->getEndDateTime())->format('%a');

        $appoints      = [];
        $incrementDate = clone $options->getStartDateTime();

        $intervalMatrix = $options->getIntervalMatrix();

        for ($i = 0; $i <= $diffDateCount; $i++) {
            $intervalMax = [];
            for ($s = 0; $s < 60 / $options->getInterval(); $s++) {
                $key = $options->getInterval() * $s;

                $intervalMax[$key] = ($intervalMatrix[$key]) * ($options->getEndTime() - $options->getStartTime());
            }

            $incrementDateStation = clone $incrementDate;

            for ($station = 0; $station < $options->getMaxIntervalValues(); $station++) {
                $incrementDateStation = clone $incrementDate;

                $stationsList = [];
                while ($incrementDateStation < new DateTime($incrementDateStation->format('Y-m-') . str_pad((string) $incrementDay, 2, '0', STR_PAD_LEFT) . " " . $options->getEndDateTime()->format('H:i'))) {
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

                    if ($intervalMax[(int) $incrementDateStation->format('i')] < 0) {
                        $incrementDateStation->add(new DateInterval('PT' . $options->getInterval() . 'M'));

                        continue;
                    }

                    if ($options->hasDiffInterval()) {
                        $intervalMax[(int) $incrementDateStation->format('i')]--;

                        if ($intervalMax[(int) $incrementDateStation->format('i')] < 0) {
                            $incrementDateStation->add(new DateInterval('PT' . $options->getInterval() . 'M'));

                            continue;
                        }
                    } else {
                        $intervalMax[(int) $incrementDateStation->format('i')]--;
                    }

                    $stationsList[] = clone $incrementDateStation;

                    $incrementDateStation->add(new DateInterval('PT' . $options->getInterval() . 'M'));
                }

                $appoints = array_merge($appoints, $stationsList);
            }

            $incrementDay++;

            $incrementDate = clone new DateTime($incrementDateStation->format('Y-m-') . str_pad((string) $incrementDay, 2, '0', STR_PAD_LEFT) . " " . $options->getStartDateTime()->format('H:i'));
        }

        return $appoints;
    }
}

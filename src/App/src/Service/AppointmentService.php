<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Appointment;
use App\Entity\Place;
use App\Repository\AppointmentRepositoryInterface;
use App\Repository\PlaceRepositoryInterface;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

use function array_merge;
use function str_pad;

use const STR_PAD_LEFT;

final class AppointmentService implements AppointmentServiceInterface
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var AppointmentRepositoryInterface */
    protected $appointmentRepository;

    /** @var PlaceRepositoryInterface */
    protected $placeRepository;

    /** @var array */
    protected $config;

    public function __construct(array $config, EntityManagerInterface $em)
    {
        $this->config                = $config;
        $this->em                    = $em;
        $this->appointmentRepository = $this->em->getRepository(Appointment::class);
        $this->placeRepository       = $this->em->getRepository(Place::class);
    }

    public function getRepository(): AppointmentRepositoryInterface
    {
        return $this->appointmentRepository;
    }

    public function clearExpiredData(): void
    {
        $appointments = $this->appointmentRepository->findAll();

        foreach ($appointments as $appointment) {
            $boundaryDate = new DateTime();
            $boundaryDate->add(new DateInterval('P1D'));
            $boundaryDate = $boundaryDate->setTime(
                (int) $this->config['app']['appointment']['expired_time_hour'],
                (int) $this->config['app']['appointment']['expired_time_min'],
            );

            if ($appointment->getAvailable() && $appointment->getDate() <= $boundaryDate) {
                $appointment->setBanned(true);
            }
        }

        $this->em->flush();
    }

    private function getEmptyEntities(bool $normalLunchTime = true): array
    {
        $startTime = 8;
        $endTime   = 19;
        $startDate = new DateTime("2020-12-19 " . str_pad((string) $startTime, 2, '0', STR_PAD_LEFT) . ":00");
        $endDate   = new DateTime("2020-12-23 " . str_pad((string) $endTime, 2, '0', STR_PAD_LEFT) . ":00");
        $minutes   = 10;

        $incrementDay  = (int) $startDate->format('d');
        $diffDateCount = (int) $startDate->diff($endDate)->format('%a');

        $appoints      = [];
        $incrementDate = clone $startDate;

        for ($i = 0; $i <= $diffDateCount; $i++) {
            $stationMax = [
                0  => 5 * ($endTime - $startTime - 2),
                10 => 5 * ($endTime - $startTime - 2),
                20 => 5 * ($endTime - $startTime - 2),
                30 => 5 * ($endTime - $startTime - 2),
                40 => 4 * ($endTime - $startTime - 2),
                50 => 4 * ($endTime - $startTime - 2),
            ];

            for ($station = 0; $station <= 4; $station++) {
                $incrementDateStation = clone $incrementDate;

                $stationsList = [];
                while ($incrementDateStation < new DateTime("2020-12-" . str_pad((string) $incrementDay, 2, '0', STR_PAD_LEFT) . " 18:00")) {
                    if ($normalLunchTime) {
                        if (
                            ($incrementDateStation >= new DateTime("2020-12-" . str_pad((string) $incrementDay, 2, '0', STR_PAD_LEFT) . " 12:00")) &&
                            ($incrementDateStation < new DateTime("2020-12-" . str_pad((string) $incrementDay, 2, '0', STR_PAD_LEFT) . " 13:00"))
                        ) {
                            $incrementDateStation->add(new DateInterval('PT' . $minutes . 'M'));

                            continue;
                        }
                    } else {
                        if (
                            ($incrementDateStation >= new DateTime("2020-12-" . str_pad((string) $incrementDay, 2, '0', STR_PAD_LEFT) . " 13:00")) &&
                            ($incrementDateStation < new DateTime("2020-12-" . str_pad((string) $incrementDay, 2, '0', STR_PAD_LEFT) . " 14:00"))
                        ) {
                            $incrementDateStation->add(new DateInterval('PT' . $minutes . 'M'));

                            continue;
                        }
                    }

                    if ($stationMax[(int) $incrementDateStation->format('i')] <= 0) {
                        $incrementDateStation->add(new DateInterval('PT' . $minutes . 'M'));

                        continue;
                    }

                    $stationMax[(int) $incrementDateStation->format('i')]--;
                    $stationsList[] = clone $incrementDateStation;

                    $incrementDateStation->add(new DateInterval('PT' . $minutes . 'M'));
                }

                $appoints = array_merge($appoints, $stationsList);
            }

            $incrementDay++;

            $incrementDate = clone new DateTime("2020-12-" . str_pad((string) $incrementDay, 2, '0', STR_PAD_LEFT) . " " . str_pad((string) $startTime, 2, '0', STR_PAD_LEFT) . ":00");
        }

        return $appoints;
    }

    public function generateEmptyEntities(): void
    {
        $places = $this->placeRepository->findBy([
            'active' => true,
        ]);

        foreach ($places as $place) {
            $appoints = $this->getEmptyEntities($place->getType() === 1);

            foreach ($appoints as $date) {
                $now = new DateTime();

                $appointment = new Appointment();

                $appointment->setActive(true);
                $appointment->setPlace($place);
                $appointment->setDate($date);
                $appointment->setCreatedAt($now);
                $appointment->setUpdatedAt($now);

                $this->em->persist($appointment);
            }
        }

        $this->em->flush();
    }
}

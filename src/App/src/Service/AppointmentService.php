<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Appointment;
use App\Entity\Place;
use App\Generator\AppointmentGenerator;
use App\Generator\AppointmentGeneratorOptionsInterface;
use App\Repository\AppointmentRepositoryInterface;
use App\Repository\PlaceRepositoryInterface;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

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

    private function getClearDateTime(): DateTime
    {
        $boundaryDate = new DateTime();

        if (
            ! isset($this->config['app']['appointment']['expired_time_enable']) ||
            (int) $this->config['app']['appointment']['expired_time_enable'] === 0
        ) {
            return $boundaryDate;
        }

        if (
            isset($this->config['app']['appointment']['expired_time_day_is_plus']) &&
            (int) $this->config['app']['appointment']['expired_time_day_is_plus'] === 1
        ) {
            $boundaryDate->add(new DateInterval('P1D'));
        }

        $boundaryDate = $boundaryDate->setTime(
            (int) $this->config['app']['appointment']['expired_time_hour'],
            (int) $this->config['app']['appointment']['expired_time_min'],
        );

        return $boundaryDate;
    }

    public function clearExpiredData(): void
    {
        $appointments = $this->appointmentRepository->findAll();

        foreach ($appointments as $appointment) {
            $boundaryDate = $this->getClearDateTime();

            if ($appointment->getAvailable() && $appointment->getDate() <= $boundaryDate) {
                $appointment->setBanned(true);
            }
        }

        $this->em->flush();
    }

    public function generateEmptyEntities(AppointmentGeneratorOptionsInterface $appGenOptions): void
    {
        $places = $this->placeRepository->findBy([
            'active' => true,
        ]);

        foreach ($places as $place) {
            $options = clone $appGenOptions;
            $options->setNormalLunchTime($place->getType() === 1);

            $appoints = (new AppointmentGenerator())->getDates($options);

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

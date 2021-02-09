<?php

declare(strict_types=1);

namespace AppTest\Service;

use App\Entity\Appointment;
use App\Entity\Place;
use App\Generator\AppointmentGenerator;
use App\Generator\AppointmentGeneratorOptions;
use App\Repository\AppointmentRepository;
use App\Repository\PlaceRepository;
use AppTest\AbstractServiceTest;
use DateTime;
use Doctrine\ORM\Mapping\ClassMetadata;
use DoctrineFixture\ApplicantDataLoader;
use DoctrineFixture\AppointmentDataLoader;
use DoctrineFixture\FixtureManager;
use DoctrineFixture\PlaceDataLoader;

use function count;

class AppointmentServiceTest extends AbstractServiceTest
{
    protected function setUp(): void
    {
        $this->appointmentRepository = new AppointmentRepository(
            FixtureManager::getEntityManager(),
            new ClassMetadata(Appointment::class)
        );

        $this->placeRepository = new PlaceRepository(
            FixtureManager::getEntityManager(),
            new ClassMetadata(Place::class)
        );

        $this->fixtureExecutor = FixtureManager::getFixtureExecutor();
    }

    public function testReturnPlaceInstance()
    {
        $this->fixtureExecutor->execute([
            new PlaceDataLoader(),
            new AppointmentDataLoader(),
            new ApplicantDataLoader(),
        ]);

        $this->assertInstanceOf(Place::class, $this->placeRepository->find(1));
    }

    public function testInsertedAppointments()
    {
        $this->fixtureExecutor->execute([
            new PlaceDataLoader(),
            new AppointmentDataLoader(),
            new ApplicantDataLoader(),
        ]);

        $appointments = $this->appointmentRepository->findAll([]);

        $originalAppointmentCount = count($appointments);

        $places = $this->placeRepository->findBy([
            'active' => true,
        ]);

        $this->assertCount(2, $places);

        $appGenOptions = new AppointmentGeneratorOptions();
        $appGenOptions->setStartTime(8);
        $appGenOptions->setEndTime(18);
        $appGenOptions->setStartDate(new DateTime("2021-02-08"));
        $appGenOptions->setEndDate(new DateTime("2021-02-08"));
        $appGenOptions->setNormalLunchTime(true);
        $appGenOptions->setIntervalMatrix([
            0  => 1,
            10 => 1,
            20 => 1,
            30 => 1,
            40 => 1,
            50 => 1,
        ]);

        $em = FixtureManager::getEntityManager();

        foreach ($places as $place) {
            $appoints = (new AppointmentGenerator())->getDates($appGenOptions);

            foreach ($appoints as $date) {
                $now = new DateTime();

                $appointment = new Appointment();

                $appointment->setActive(true);
                $appointment->setPlace($place);
                $appointment->setDate($date);
                $appointment->setCreatedAt($now);
                $appointment->setUpdatedAt($now);

                $em->persist($appointment);
            }
        }

        $em->flush();

        $appointments = $this->appointmentRepository->findAll();

        $newAppointmentCount = count($appointments);

        $this->assertEquals(54 * 2, $newAppointmentCount - $originalAppointmentCount);
    }
}

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
use DateInterval;
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
        $appGenOptions->setStartDateTime(new DateTime("2021-02-08 08:00"));
        $appGenOptions->setEndDateTime(new DateTime("2021-02-08 18:00"));
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

    public function testClearExpiredAppointmentItsTime()
    {
        $appointment = $this->getMockBuilder(Appointment::class)->getMock();
        $appointment->method('getDate')->willReturn(new DateTime('2020-12-14 08:00:00.000000'));
        $appointment->method('getAvailable')->willReturn(true);

        $boundaryDate = new DateTime('2020-12-14 16:00:00.000000');

        $this->assertEquals(true, $appointment->getAvailable() && $appointment->getDate() <= $boundaryDate);
    }

    public function testClearExpiredAppointmentItsTimeEquals()
    {
        $appointment = $this->getMockBuilder(Appointment::class)->getMock();
        $appointment->method('getDate')->willReturn(new DateTime('2020-12-14 08:00:00.000000'));
        $appointment->method('getAvailable')->willReturn(true);

        $boundaryDate = new DateTime('2020-12-14 08:00:00.000000');

        $this->assertEquals(true, $appointment->getAvailable() && $appointment->getDate() <= $boundaryDate);
    }

    public function testClearExpiredAppointmentNotYetTime()
    {
        $appointment = $this->getMockBuilder(Appointment::class)->getMock();
        $appointment->method('getDate')->willReturn(new DateTime('2020-12-14 08:00:00.000000'));
        $appointment->method('getAvailable')->willReturn(true);

        $boundaryDate = new DateTime('2020-12-10 12:00:00.000000');
        $boundaryDate = $boundaryDate->setTime(16, 0);

        $this->assertEquals(false, $appointment->getAvailable() && $appointment->getDate() <= $boundaryDate);
    }
}

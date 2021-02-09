<?php

declare(strict_types=1);

namespace DoctrineFixture;

use App\Entity\Appointment;
use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AppointmentDataLoader extends AbstractFixture implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $appointment = new Appointment();
        $appointment->setId(1);
        $appointment->setDate(new DateTime('2020-12-14 08:00:00'));
        $appointment->setPlace(
            $this->getReference('place-1')
        );
        $appointment->setPhase(0);
        $appointment->setCreatedAt(new DateTime());
        $appointment->setUpdatedAt(new DateTime());

        $manager->persist($appointment);
        $manager->flush();

        $this->addReference('appointment-1', $appointment);
    }
}

<?php

declare(strict_types=1);

namespace DoctrineFixture;

use App\Entity\Applicant;
use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ApplicantDataLoader extends AbstractFixture implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $applicant = new Applicant();
        $applicant->setId(1);
        $applicant->setAppointment(
            $this->getReference('appointment-1')
        );
        $applicant->setCancelHash('VmRPNjllU0RwOFNY');
        $applicant->setLastname('Kovács');
        $applicant->setFirstname('János');
        $applicant->setHumanId('D1-00001');
        $applicant->setAddress('1052 Budapest, Városház utca 0');
        $applicant->setTaj('111 222 333');
        $applicant->setPhone('36300001122');
        $applicant->setEmail('john.smith@test.hu');
        $applicant->setBirthdayPlace('Budapest');
        $applicant->setBirthday(new DateTime('1990-01-01'));
        $applicant->setActive(true);
        $applicant->setCreatedAt(new DateTime());
        $applicant->setUpdatedAt(new DateTime());

        $manager->persist($applicant);
        $manager->flush();
    }
}

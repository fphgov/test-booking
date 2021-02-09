<?php

declare(strict_types=1);

namespace DoctrineFixture;

use App\Entity\Applicant;
use App\Entity\ApplicantInterface;
use App\Entity\Appointment;
use App\Entity\AppointmentInterface;
use App\Entity\Place;
use App\Entity\PlaceInterface;
use App\Entity\Reservation;
use App\Entity\ReservationInterface;
use App\Entity\Setting;
use App\Entity\SettingInterface;
use App\Entity\User;
use App\Entity\UserInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\ResolveTargetEntityListener;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;

use function dirname;

final class FixtureManager
{
    public static function getEntityManager(): EntityManagerInterface
    {
        $isDevMode = true;

        $paths = [
            dirname(__DIR__, 2) . '/src/App/src/Entity',
        ];

        $connectionParams = [
            'driver' => 'pdo_sqlite',
            'url'    => 'sqlite:////usr/local/var/db.sqlite',
        ];

        $config = Setup::createConfiguration($isDevMode);
        $driver = new AnnotationDriver(new AnnotationReader(), $paths);

        AnnotationRegistry::registerLoader('class_exists');
        $config->setMetadataDriverImpl($driver);

        $eventManager = new EventManager();
        $rtel         = new ResolveTargetEntityListener();

        $rtel->addResolveTargetEntity(ApplicantInterface::class, Applicant::class, []);
        $rtel->addResolveTargetEntity(AppointmentInterface::class, Appointment::class, []);
        $rtel->addResolveTargetEntity(PlaceInterface::class, Place::class, []);
        $rtel->addResolveTargetEntity(ReservationInterface::class, Reservation::class, []);
        $rtel->addResolveTargetEntity(SettingInterface::class, Setting::class, []);
        $rtel->addResolveTargetEntity(UserInterface::class, User::class, []);

        $eventManager->addEventListener(Events::loadClassMetadata, $rtel);

        return EntityManager::create($connectionParams, $config, $eventManager);
    }

    public static function start(): void
    {
        $em = static::getEntityManager();

        $schemaTool = new SchemaTool(static::getEntityManager());
        $metadatas  = static::getEntityManager()
                            ->getMetadataFactory()
                            ->getAllMetadata();

        $schemaTool->dropSchema($metadatas);
        $schemaTool->createSchema($metadatas);
    }

    public static function getFixtureExecutor(): ORMExecutor
    {
        return new ORMExecutor(
            static::getEntityManager(),
            new ORMPurger(static::getEntityManager())
        );
    }
}

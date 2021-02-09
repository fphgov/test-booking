<?php

declare(strict_types=1);

namespace DoctrineFixture;

use App\Entity\Place;
use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PlaceDataLoader extends AbstractFixture implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $place = new Place();
        $place->setId(1);
        $place->setName('Deák tér');
        $place->setDescription('Deák Ferenc téri buszmegálló melletti zöld terület');
        $place->setType(1);
        $place->setShortName('D1');
        $place->setLink('https://goo.gl/maps/JrDBusFn2nxYnMBR7');
        $place->setActive(true);
        $place->setCreatedAt(new DateTime());
        $place->setUpdatedAt(new DateTime());

        $manager->persist($place);
        $manager->flush();

        $this->addReference('place-1', $place);

        $place = new Place();
        $place->setId(2);
        $place->setName('Keleti');
        $place->setDescription('Keleti pályaudvar főbejárata mellett');
        $place->setType(2);
        $place->setShortName('K1');
        $place->setLink('https://goo.gl/maps/MHWKkNN3BYbXydJo6');
        $place->setActive(true);
        $place->setCreatedAt(new DateTime());
        $place->setUpdatedAt(new DateTime());

        $manager->persist($place);
        $manager->flush();

        $this->addReference('place-2', $place);
    }
}

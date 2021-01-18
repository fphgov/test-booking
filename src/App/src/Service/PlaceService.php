<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Place;
use App\Repository\PlaceRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class PlaceService implements PlaceServiceInterface
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var PlaceRepositoryInterface */
    protected $placeRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em              = $em;
        $this->placeRepository = $this->em->getRepository(Place::class);
    }

    public function getRepository(): PlaceRepositoryInterface
    {
        return $this->placeRepository;
    }
}

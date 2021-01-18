<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\PlaceRepositoryInterface;

interface PlaceServiceInterface
{
    public function getRepository(): PlaceRepositoryInterface;
}

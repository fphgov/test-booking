<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\AppointmentRepositoryInterface;

interface AppointmentServiceInterface
{
    public function getRepository(): AppointmentRepositoryInterface;

    public function generateEmptyEntities(): void;
}

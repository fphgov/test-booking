<?php

declare(strict_types=1);

namespace App\Service;

use App\Generator\AppointmentGeneratorOptionsInterface;
use App\Repository\AppointmentRepositoryInterface;

interface AppointmentServiceInterface
{
    public function getRepository(): AppointmentRepositoryInterface;

    public function clearExpiredData(): void;

    public function generateEmptyEntities(AppointmentGeneratorOptionsInterface $appGenOptions): void;
}

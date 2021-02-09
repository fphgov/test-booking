<?php

declare(strict_types=1);

namespace App\Generator;

interface AppointmentGeneratorInterface
{
    public function getDates(AppointmentGeneratorOptionsInterface $options): array;
}

<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\SettingInterface;
use App\Repository\SettingRepositoryInterface;

interface SettingServiceInterface
{
    public function getRepository(): SettingRepositoryInterface;

    public function modifySetting(array $body): SettingInterface;
}

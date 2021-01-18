<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\UserRepositoryInterface;

interface UserServiceInterface
{
    public function getRepository(): UserRepositoryInterface;
}

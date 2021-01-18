<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

final class UserRepository extends EntityRepository implements UserRepositoryInterface
{
}

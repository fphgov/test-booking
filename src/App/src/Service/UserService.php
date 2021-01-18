<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class UserService implements UserServiceInterface
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var UserRepositoryInterface */
    protected $userRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em             = $em;
        $this->userRepository = $this->em->getRepository(User::class);
    }

    public function getRepository(): UserRepositoryInterface
    {
        return $this->userRepository;
    }
}

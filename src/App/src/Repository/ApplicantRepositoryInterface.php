<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ApplicantInterface;
use DateTime;
use Doctrine\Persistence\ObjectRepository;

interface ApplicantRepositoryInterface extends ObjectRepository
{
    /** @return ApplicantInterface[] */
    public function quickSearch(string $search);

    /** @return mixed|int */
    public function getCount();

    /** @return ApplicantInterface[] */
    public function getApplicantsByDate(DateTime $date, int $limit = 10);
}

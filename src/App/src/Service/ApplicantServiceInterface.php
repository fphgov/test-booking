<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ApplicantInterface;
use App\Repository\ApplicantRepositoryInterface;

interface ApplicantServiceInterface
{
    public function getRepository(): ApplicantRepositoryInterface;

    public function addApplicant(array $filteredParams, string $sessionId): ApplicantInterface;

    public function removeApplication(ApplicantInterface $applicant): bool;
}

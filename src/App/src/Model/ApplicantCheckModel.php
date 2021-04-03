<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Applicant;
use App\Service\ApplicantServiceInterface;

final class ApplicantCheckModel
{
    private array $applicants = [];

    /** @param Applicant[] **/
    public function parseEntities(array $models): void
    {
        $this->applicants = [];

        foreach ($models as $model) {
            if ($model instanceof Applicant) {
                $this->applicants[] = $this->parseModel($model);
            }
        }
    }

    public function getModels(): array
    {
        return $this->applicants;
    }

    public function parseModel(Applicant $applicant): array
    {
        return [
            'id'          => $applicant->getId(),
            'humanId'     => $applicant->getHumanId(),
            'firstname'   => $applicant->getFirstname(),
            'lastname'    => $applicant->getLastname(),
            'location'    => $applicant->getAppointment()->getPlace()->getName(),
            'appointment' => $applicant->getAppointment()->getDate()->format('Y-m-d H:i:s'),
            'notified'    => $applicant->getNotified(),
            'attended'    => $applicant->getAttended(),
        ];
    }
}

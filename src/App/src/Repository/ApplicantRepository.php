<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Applicant;
use App\Entity\Appointment;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

use function preg_grep;
use function preg_quote;
use function strtoupper;

final class ApplicantRepository extends EntityRepository implements ApplicantRepositoryInterface
{
    /** @return Applicant[] */
    public function quickSearch(string $search)
    {
        $input        = preg_quote($search, '~');
        $allApplicant = $this->findAll();

        $applicants = [];
        foreach ($allApplicant as $applicant) {
            $fields = [
                strtoupper($applicant->getHumanId()),
                strtoupper($applicant->getEmail()),
                strtoupper($applicant->getFirstname()),
                strtoupper($applicant->getLastname()),
                strtoupper($applicant->getPhone()),
            ];

            if (preg_grep('~' . strtoupper($input) . '~', $fields)) {
                $applicants[] = $applicant;
            }
        }

        return $applicants;
    }

    /** @return Applicant[] */
    public function quickAdvancedSearch(string $search)
    {
        $input        = preg_quote($search, '~');
        $allApplicant = $this->findAll();

        $applicants = [];
        foreach ($allApplicant as $applicant) {
            $fields = [
                strtoupper($applicant->getHumanId()),
                strtoupper($applicant->getEmail()),
                strtoupper($applicant->getFirstname()),
                strtoupper($applicant->getLastname()),
                strtoupper($applicant->getPhone()),
                strtoupper($applicant->getTaj()),
            ];

            if (preg_grep('~' . strtoupper($input) . '~', $fields)) {
                $applicants[] = $applicant;
            }
        }

        return $applicants;
    }

    /** @return mixed|int */
    public function getCount()
    {
        $qb = $this->createQueryBuilder("a");
        $qb
            ->select("COUNT(1)");

        return $qb->getQuery()->getSingleScalarResult();
    }

    /** @return Applicant[] */
    public function getApplicantsByDate(DateTime $date, int $limit = 10)
    {
        $from = new DateTime($date->format("Y-m-d") . " 00:00:00");
        $to   = new DateTime($date->format("Y-m-d") . " 23:59:59");

        $qb = $this->createQueryBuilder('a');
        $qb
            ->innerJoin(Appointment::class, 'app', Join::WITH, 'app.id = a.appointment')
            ->where('app.date BETWEEN :from AND :to')
            ->andWhere('a.survey = :survey')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->setParameter('survey', false)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /** @return Applicant[] */
    public function getApplicantsToReminder(DateTime $date, int $limit = 10)
    {
        $from = new DateTime($date->format("Y-m-d") . " 00:00:00");
        $to   = new DateTime($date->format("Y-m-d") . " 23:59:59");

        $qb = $this->createQueryBuilder('a');
        $qb
            ->innerJoin(Appointment::class, 'app', Join::WITH, 'app.id = a.appointment')
            ->where('app.date BETWEEN :from AND :to')
            ->andWhere('a.reminder = :reminder')
            ->andWhere('a.attended = :attended')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->setParameter('reminder', false)
            ->setParameter('attended', false)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }
}

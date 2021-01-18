<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Appointment;
use App\Entity\Reservation;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

final class AppointmentRepository extends EntityRepository implements AppointmentRepositoryInterface
{
    /** @return Appointment[] */
    public function belongingToPlace(int $placeId, DateTime $date, int $phase = 0)
    {
        $from = new DateTime($date->format("Y-m-d") . " 00:00:00");
        $to   = new DateTime($date->format("Y-m-d") . " 23:59:59");

        $qb = $this->createQueryBuilder("a");
        $qb
            ->select('a.id, a.banned, a.date, res.expiry')
            ->leftJoin(Reservation::class, 'res', Join::WITH, 'res.appointment = a.id')
            ->where('a.date BETWEEN :from AND :to')
            ->andWhere('a.place = :place')
            ->andWhere('a.banned = :banned')
            ->andWhere('a.active = :active')
            ->andWhere('a.phase <= :phase')
            ->orderBy('a.date', 'asc')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->setParameter('place', $placeId)
            ->setParameter('active', true)
            ->setParameter('banned', false)
            ->setParameter('phase', $phase);

        return $qb->getQuery()->getResult();
    }

    public function belongingToPlaceTime(int $placeId, int $phase = 0): array
    {
        $qb = $this->createQueryBuilder("a");
        $qb
            ->select("DATE_FORMAT(a.date, '%Y-%m-%d') as date")
            ->where('a.place = :place')
            ->andWhere('a.banned = :banned')
            ->andWhere('a.active = :active')
            ->andWhere('a.phase <= :phase')
            ->groupBy("date")
            ->setParameter('place', $placeId)
            ->setParameter('active', true)
            ->setParameter('banned', false)
            ->setParameter('phase', $phase);

        $result = $qb->getQuery()->getResult();

        $res = [];

        foreach ($result as $r) {
            $res[] = $r['date'];
        }

        return $res;
    }

    /** @return mixed|int */
    public function getAvailableAppointments(int $phase = 0)
    {
        $qb = $this->createQueryBuilder("a");
        $qb
            ->select("COUNT(1)")
            ->andWhere('a.banned = :banned')
            ->andWhere('a.active = :active')
            ->andWhere('a.phase <= :phase')
            ->setParameter('active', true)
            ->setParameter('banned', false)
            ->setParameter('phase', $phase);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /** @return mixed|int */
    public function getBannedAppointments(int $phase = 0)
    {
        $qb = $this->createQueryBuilder("a");
        $qb
            ->select("COUNT(1)")
            ->andWhere('a.banned = :banned')
            ->andWhere('a.active = :active')
            ->andWhere('a.phase <= :phase')
            ->setParameter('active', true)
            ->setParameter('banned', true)
            ->setParameter('phase', $phase);

        return $qb->getQuery()->getSingleScalarResult();
    }
}

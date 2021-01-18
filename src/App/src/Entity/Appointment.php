<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\AppointmentInterface;
use App\Traits\EntityMetaTrait;
use App\Traits\EntityTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

use function getenv;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AppointmentRepository")
 * @ORM\Table(name="appointments")
 */
class Appointment implements JsonSerializable, AppointmentInterface
{
    use EntityMetaTrait;
    use EntityTrait;

    public const DISABLE_SHOW_DEFAULT = [
        'place',
        'phase',
        'active',
        'createdAt',
        'updatedAt',
    ];

    /**
     * @ORM\ManyToOne(targetEntity="PlaceInterface")
     * @ORM\JoinColumn(name="placeId", referencedColumnName="id", nullable=false)
     *
     * @var PlaceInterface
     */
    private $place;

    /**
     * @ORM\Column(name="date", type="datetime", nullable=false)
     *
     * @var DateTime
     */
    private $date;

    /**
     * @ORM\Column(name="phase", type="smallint", nullable=false, options={"unsigned"=true})
     *
     * @var int
     */
    private $phase = 0;

    /**
     * @ORM\Column(name="banned", type="boolean")
     *
     * @var bool
     */
    private $banned = false;

    public function setPlace(PlaceInterface $place): void
    {
        $this->place = $place;
    }

    public function getPlace(): PlaceInterface
    {
        return $this->place;
    }

    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setPhase(int $phase): void
    {
        $this->phase = $phase;
    }

    public function getPhase(): int
    {
        return $this->phase;
    }

    public function setBanned(bool $banned): void
    {
        $this->banned = $banned;
    }

    public function getBanned(): bool
    {
        return (bool) $this->banned;
    }

    public function getAvailable(): bool
    {
        return ! $this->getBanned() && $this->getActive() && $this->getPhase() <= (int) getenv('APP_PHASE');
    }
}

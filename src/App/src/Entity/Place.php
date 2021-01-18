<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\EntityMetaTrait;
use App\Traits\EntityTrait;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlaceRepository")
 * @ORM\Table(name="places")
 */
class Place implements JsonSerializable, PlaceInterface
{
    use EntityMetaTrait;
    use EntityTrait;

    public const DISABLE_SHOW_DEFAULT = [
        'type',
        'createdAt',
        'updatedAt',
    ];

    /**
     * @ORM\Column(name="name", type="string", nullable=false)
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(name="description", type="string", nullable=false)
     *
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(name="type", type="integer", nullable=false)
     *
     * @var int
     */
    private $type;

    /**
     * @ORM\Column(name="shortName", type="string", length=2)
     *
     * @var string
     */
    private $shortName;

    /**
     * @ORM\Column(name="link", type="text")
     *
     * @var string
     */
    private $link;

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setType(int $type): void
    {
        $this->type = $type;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setShortName(string $shortName): void
    {
        $this->shortName = $shortName;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    public function getLink(): string
    {
        return $this->link;
    }
}

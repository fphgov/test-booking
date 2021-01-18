<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\EntityMetaTrait;
use App\Traits\EntityTrait;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SettingRepository")
 * @ORM\Table(name="settings")
 */
class Setting implements JsonSerializable, SettingInterface
{
    use EntityMetaTrait;
    use EntityTrait;

    public const DISABLE_SHOW_DEFAULT = [
        'id',
        'active',
        'createdAt',
        'updatedAt',
    ];

    /**
     * @ORM\Column(name="close", type="boolean")
     *
     * @var bool
     */
    private $close = false;

    public function setClose(bool $close): void
    {
        $this->close = $close;
    }

    public function getClose(): bool
    {
        return (bool) $this->close;
    }
}

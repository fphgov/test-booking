<?php

declare(strict_types=1);

namespace App\Entity;

use App\Interfaces\EntityInterface;

interface PlaceInterface extends EntityInterface
{
    public function getActive(): bool;

    public function setActive(bool $active): void;

    public function setName(string $name): void;

    public function getName(): string;

    public function setDescription(string $description): void;

    public function getDescription(): string;

    public function setType(int $type): void;

    public function getType(): int;

    public function setShortName(string $shortName): void;

    public function getShortName(): string;

    public function setLink(string $link): void;

    public function getLink(): string;
}

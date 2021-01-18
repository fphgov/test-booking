<?php

declare(strict_types=1);

namespace App\Interfaces;

interface EntityInterface
{
    public const DISABLE_DEFAULT_SET = [
        'id',
        'image',
        'password',
        'createdAt',
        'updatedAt',
    ];

    public function getId(): int;

    public function setId(int $id): void;

    public function setProps(array $datas): void;

    public function getProps(): array;

    public function jsonSerialize(): array;

    public function toArray(): array;
}

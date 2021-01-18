<?php

declare(strict_types=1);

namespace App\Entity;

use App\Interfaces\EntityInterface;

interface UserInterface extends EntityInterface
{
    public function getActive(): bool;

    public function setActive(bool $active): void;

    public function setFirstname(string $firstname): self;

    public function getFirstname(): string;

    public function setLastname(string $lastname): self;

    public function getLastname(): string;

    public function setEmail(string $email): self;

    public function getEmail(): string;

    public function setPassword(string $password): self;

    public function getPassword(): ?string;

    public function setRole(string $role): self;

    public function getRole(): ?string;

    public function generateToken(): string;
}

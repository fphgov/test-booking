<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\EntityMetaTrait;
use App\Traits\EntityTrait;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="users")
 */
class User implements JsonSerializable, UserInterface
{
    use EntityMetaTrait;
    use EntityTrait;

    public const DISABLE_SHOW_DEFAULT = [
        'password',
        'createdAt',
        'updatedAt',
    ];

    /**
     * @ORM\Column(name="firstname", type="string")
     *
     * @var string
     */
    private $firstname;

    /**
     * @ORM\Column(name="lastname", type="string")
     *
     * @var string
     */
    private $lastname;

    /**
     * @ORM\Column(name="email", type="string", length=100, unique=true)
     *
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(name="password", type="text", length=100)
     *
     * @var string
     */
    private $password;

    /**
     * @ORM\Column(name="role", type="string")
     *
     * @var string
     */
    private $role;

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function generateToken(): string
    {
        $uuid4 = Uuid::uuid4();

        return $uuid4->toString();
    }
}

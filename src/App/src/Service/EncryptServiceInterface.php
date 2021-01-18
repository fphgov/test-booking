<?php

declare(strict_types=1);

namespace App\Service;

interface EncryptServiceInterface
{
    public const ENCRYPT_KEY = 'doctrine_encrypt';

    public function encrypt(string $value): string;

    public function decrypt(string $value): string;
}

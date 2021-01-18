<?php

declare(strict_types=1);

namespace App\Service;

use function base64_decode;
use function base64_encode;
use function hash;
use function openssl_decrypt;
use function openssl_encrypt;
use function substr;

final class EncryptService implements EncryptServiceInterface
{
    /** @var string */
    private $key;

    /** @var string */
    private $iv;

    /** @var array */
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;

        $this->key = hash($this->config['sha_type'], $this->config['secret_key']);
        $this->iv  = substr(hash($this->config['sha_type'], $this->config['secret_iv']), 0, 16);
    }

    public function encrypt(string $value): string
    {
        return base64_encode(openssl_encrypt($value, $this->config['encrypt_method'], $this->key, 0, $this->iv));
    }

    public function decrypt(string $value): string
    {
        return openssl_decrypt(base64_decode($value), $this->config['encrypt_method'], $this->key, 0, $this->iv);
    }
}

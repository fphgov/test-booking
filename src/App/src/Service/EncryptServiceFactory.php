<?php

declare(strict_types=1);

namespace App\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;

final class EncryptServiceFactory
{
    /**
     * @return EncryptService
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->has('config') ? $container->get('config') : [];

        if (
            ! isset($config[EncryptServiceInterface::ENCRYPT_KEY]) ||
            ! isset($config[EncryptServiceInterface::ENCRYPT_KEY]['sha_type'])
        ) {
            throw new ServiceNotCreatedException('Missing sha_type configuration');
        }

        if (
            ! isset($config[EncryptServiceInterface::ENCRYPT_KEY]) ||
            ! isset($config[EncryptServiceInterface::ENCRYPT_KEY]['encrypt_method'])
        ) {
            throw new ServiceNotCreatedException('Missing encrypt_method configuration');
        }

        if (
            ! isset($config[EncryptServiceInterface::ENCRYPT_KEY]) ||
            ! isset($config[EncryptServiceInterface::ENCRYPT_KEY]['secret_key'])
        ) {
            throw new ServiceNotCreatedException('Missing secret_key configuration');
        }

        if (
            ! isset($config[EncryptServiceInterface::ENCRYPT_KEY]) ||
            ! isset($config[EncryptServiceInterface::ENCRYPT_KEY]['secret_iv'])
        ) {
            throw new ServiceNotCreatedException('Missing secret_iv configuration');
        }

        return new EncryptService($config[EncryptServiceInterface::ENCRYPT_KEY]);
    }
}

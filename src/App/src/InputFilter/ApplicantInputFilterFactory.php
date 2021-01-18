<?php

declare(strict_types=1);

namespace App\InputFilter;

use App\Service\EncryptServiceInterface;
use Interop\Container\ContainerInterface;
use Laminas\Db\Adapter\AdapterInterface;

final class ApplicantInputFilterFactory
{
    public function __invoke(ContainerInterface $container): ApplicantInputFilter
    {
        return new ApplicantInputFilter(
            $container->get(AdapterInterface::class),
            $container->get(EncryptServiceInterface::class)
        );
    }
}

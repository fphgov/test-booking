<?php

declare(strict_types=1);

namespace App\Handler\Applicant;

use App\Service\ApplicantServiceInterface;
use Interop\Container\ContainerInterface;

final class CheckSearchHandlerFactory
{
    public function __invoke(ContainerInterface $container): CheckSearchHandler
    {
        return new CheckSearchHandler(
            $container->get(ApplicantServiceInterface::class)
        );
    }
}

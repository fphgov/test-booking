<?php

declare(strict_types=1);

namespace App\Handler\Applicant;

use App\Service\ApplicantServiceInterface;
use Interop\Container\ContainerInterface;

final class GetAllHandlerFactory
{
    public function __invoke(ContainerInterface $container): GetAllHandler
    {
        return new GetAllHandler(
            $container->get(ApplicantServiceInterface::class)
        );
    }
}

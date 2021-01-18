<?php

declare(strict_types=1);

namespace App\Handler\Applicant;

use App\Service\ApplicantServiceInterface;
use Interop\Container\ContainerInterface;

final class SearchHandlerFactory
{
    public function __invoke(ContainerInterface $container): SearchHandler
    {
        return new SearchHandler(
            $container->get(ApplicantServiceInterface::class)
        );
    }
}

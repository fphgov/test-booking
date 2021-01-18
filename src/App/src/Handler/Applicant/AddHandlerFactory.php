<?php

declare(strict_types=1);

namespace App\Handler\Applicant;

use App\InputFilter\ApplicantInputFilter;
use App\Service\ApplicantServiceInterface;
use App\Service\EncryptServiceInterface;
use App\Service\SettingServiceInterface;
use Interop\Container\ContainerInterface;
use Laminas\InputFilter\InputFilterPluginManager;

final class AddHandlerFactory
{
    public function __invoke(ContainerInterface $container): AddHandler
    {
        /** @var InputFilterPluginManager $pluginManager */
        $pluginManager = $container->get(InputFilterPluginManager::class);
        $inputFilter   = $pluginManager->get(ApplicantInputFilter::class);

        return new AddHandler(
            $inputFilter,
            $container->get(ApplicantServiceInterface::class),
            $container->get(SettingServiceInterface::class),
            $container->get(EncryptServiceInterface::class)
        );
    }
}

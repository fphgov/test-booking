<?php

declare(strict_types=1);

namespace App;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.laminas.dev/laminas-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     */
    public function __invoke(): array
    {
        return [
            'dependencies'  => $this->getDependencies(),
            'input_filters' => $this->getInputFilters(),
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [
                Handler\PingHandler::class             => Handler\PingHandler::class,
                Handler\Tools\ClearCacheHandler::class => Handler\Tools\ClearCacheHandler::class,
            ],
            'factories'  => [
                Handler\Setting\GetHandler::class             => Handler\Setting\GetHandlerFactory::class,
                Handler\Information\GetHandler::class         => Handler\Information\GetHandlerFactory::class,
                Handler\Dashboard\GetHandler::class           => Handler\Dashboard\GetHandlerFactory::class,
                Handler\Dashboard\ChangeHandler::class        => Handler\Dashboard\ChangeHandlerFactory::class,
                Handler\Applicant\AddHandler::class           => Handler\Applicant\AddHandlerFactory::class,
                Handler\Applicant\GetHandler::class           => Handler\Applicant\GetHandlerFactory::class,
                Handler\Applicant\GetAllHandler::class        => Handler\Applicant\GetAllHandlerFactory::class,
                Handler\Applicant\PostHandler::class          => Handler\Applicant\PostHandlerFactory::class,
                Handler\Applicant\SearchHandler::class        => Handler\Applicant\SearchHandlerFactory::class,
                Handler\Applicant\DelHandler::class           => Handler\Applicant\DelHandlerFactory::class,
                Handler\Applicant\ExportHandler::class        => Handler\Applicant\ExportHandlerFactory::class,
                Handler\Applicant\CancellationHandler::class  => Handler\Applicant\CancellationHandlerFactory::class,
                Handler\Applicant\CheckGetHandler::class      => Handler\Applicant\CheckGetHandlerFactory::class,
                Handler\Applicant\CheckPostHandler::class     => Handler\Applicant\CheckPostHandlerFactory::class,
                Handler\Appointment\GetHandler::class         => Handler\Appointment\GetHandlerFactory::class,
                Handler\Appointment\GetTimesHandler::class    => Handler\Appointment\GetTimesHandlerFactory::class,
                Handler\Appointment\GenerateHandler::class    => Handler\Appointment\GenerateHandlerFactory::class,
                Handler\Appointment\ReservationHandler::class => Handler\Appointment\ReservationHandlerFactory::class,
                Service\SettingServiceInterface::class        => Service\SettingServiceFactory::class,
                Service\UserServiceInterface::class           => Service\UserServiceFactory::class,
                Service\ApplicantServiceInterface::class      => Service\ApplicantServiceFactory::class,
                Service\AppointmentServiceInterface::class    => Service\AppointmentServiceFactory::class,
                Service\PlaceServiceInterface::class          => Service\PlaceServiceFactory::class,
                Service\NoticeServiceInterface::class         => Service\NoticeServiceFactory::class,
                Service\EncryptServiceInterface::class        => Service\EncryptServiceFactory::class,
                Service\ReservationServiceInterface::class    => Service\ReservationServiceFactory::class,
                Model\ApplicantExportModel::class             => Model\ApplicantExportModelFactory::class,
            ],
        ];
    }

    public function getInputFilters(): array
    {
        return [
            'factories' => [
                // InputFilter\ApplicantInputFilter::class => InvokableFactory::class,
                InputFilter\ApplicantInputFilter::class => InputFilter\ApplicantInputFilterFactory::class,
            ],
        ];
    }
}

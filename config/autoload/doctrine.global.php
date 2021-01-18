<?php

declare(strict_types=1);

use DoctrineExtensions\Query\Mysql;

$rtel = new \Doctrine\ORM\Tools\ResolveTargetEntityListener;

$rtel->addResolveTargetEntity(App\Entity\ApplicantInterface::class, App\Entity\Applicant::class, []);
$rtel->addResolveTargetEntity(App\Entity\AppointmentInterface::class, App\Entity\Appointment::class, []);
$rtel->addResolveTargetEntity(App\Entity\PlaceInterface::class, App\Entity\Place::class, []);
$rtel->addResolveTargetEntity(App\Entity\ReservationInterface::class, App\Entity\Reservation::class, []);
$rtel->addResolveTargetEntity(App\Entity\SettingInterface::class, App\Entity\Setting::class, []);
$rtel->addResolveTargetEntity(App\Entity\UserInterface::class, App\Entity\User::class, []);

return [
    'doctrine' => [
        'configuration' => [
            'orm_default' => [
                'string_functions' => [
                    'DAY'         => Mysql\Day::class,
                    'MONTH'       => Mysql\Month::class,
                    'YEAR'        => \DoctrineExtensions\Query\Mysql\Year::class,
                ],
                'datetime_functions' => [
                    'date'        => \DoctrineExtensions\Query\Mysql\Date::class,
                    'date_format' => \DoctrineExtensions\Query\Mysql\DateFormat::class,
                ]
            ]
        ],
        'connection' => [
            'orm_default' => [
                'params' => [
                    'url'           => 'mysql://'. getenv('DB_USER') .':'. getenv('DB_PASSWORD') .'@'. getenv('DB_HOSTNAME') . '/' . getenv('DB_DATABASE'),
                    'charset'       => 'utf8mb4',
                    'configuration' => []
                ],
            ],
        ],
        'event_manager' => [
            'orm_default' => [
                'subscribers' => [
                    $rtel
                ],
            ],
        ],
        'driver' => [
            'orm_default' => [
                'class'   => \Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain::class,
                'drivers' => [
                    'App\Entity' => 'my_entity',
                ],
            ],
            'my_entity' => [
                'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../../src/App/src/Entity'],
            ],
        ],
    ]
];

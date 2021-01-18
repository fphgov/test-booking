<?php

declare(strict_types=1);

return [
    'db' => [
        'driver'   => getenv('DB_DRIVER'),
        'hostname' => getenv('DB_HOSTNAME'),
        'port'     => getenv('DB_PORT'),
        'database' => getenv('DB_DATABASE'),
        'user'     => getenv('DB_USER'),
        'password' => getenv('DB_PASSWORD'),
        'charset'  => getenv('DB_CHARSET'),
        'options' => [
            'buffer_results' => true,
        ],
    ]
];

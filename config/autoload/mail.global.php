<?php

declare(strict_types=1);

use Laminas\Mail\Address;

return [
    'mail'             => [
        'smtp'     => [
            'name'              => getenv('SMTP_NAME'),
            'host'              => getenv('SMTP_HOST'),
            'port'              => getenv('SMTP_PORT'),
            'connection_class'  => getenv('SMTP_CONNECTION_CLASS'),
            'connection_config' => [
                'username' => getenv('SMTP_CONNECTION_CONFIG_USERNAME'),
                'password' => getenv('SMTP_CONNECTION_CONFIG_PASSWORD'),
                'ssl'      => getenv('SMTP_CONNECTION_CONFIG_SSL'),
            ],
        ],
        'defaults' => [
            'addFrom'     => new Address(getenv('SMTP_DEFAULTS_ADD_FROM'), getenv('SMTP_DEFAULTS_ADD_FROM_NAME')),
            'setEncoding' => 'UTF-8',
        ],
        'headers' => [
            'message_id_domain' => getenv('SMTP_HEADERS_MESSAGE_ID_DOMAIN'),
        ]
    ],
];

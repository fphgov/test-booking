<?php

declare(strict_types=1);

return [
    'log'    => [
        'writers'    => [
            'stream' => [
                'name'     => 'stream',
                'priority' => Laminas\Log\Logger::ALERT,
                'options'  => [
                    'stream'    => __DIR__ . '/../../data/log/error.log',
                    'formatter' => [
                        'name'    => Laminas\Log\Formatter\Simple::class,
                        'options' => [
                            'format'         => '%timestamp% %priorityName% (%priority%): %message% %extra%',
                            'dateTimeFormat' => 'c',
                        ],
                    ],
                    'filters'   => [
                        'priority' => [
                            'name'    => 'priority',
                            'options' => [
                                'operator' => '<=',
                                'priority' => Laminas\Log\Logger::INFO,
                            ],
                        ],
                    ],
                ],
            ],
            'db'     => [
                'name'     => 'db',
                'priority' => Laminas\Log\Logger::ALERT,
                'options'  => [
                    'db'        => new Laminas\Db\Adapter\Adapter([
                        'driver'   => getenv('DB_DRIVER'),
                        'database' => getenv('DB_DATABASE'),
                        'host'     => getenv('DB_HOSTNAME'),
                        'username' => getenv('DB_USER'),
                        'password' => getenv('DB_PASSWORD'),
                        'port'     => getenv('DB_PORT'),
                    ]),
                    'table'     => 'log_error',
                    'column'    => [
                        'timestamp'    => 'timestamp',
                        'priority'     => 'priority',
                        'priorityName' => 'priorityName',
                        'message'      => 'message',
                        'extra'        => [
                            'extra' => 'extra',
                        ],
                    ],
                    'formatter' => [
                        'name'    => Laminas\Log\Formatter\Db::class,
                        'options' => [
                            'dateTimeFormat' => 'Y-m-d h:i:s',
                        ],
                    ],
                    'filters'   => [
                        'priority' => [
                            'name'    => 'priority',
                            'options' => [
                                'operator' => '<=',
                                'priority' => Laminas\Log\Logger::INFO,
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'processors' => [
            'requestid' => [
                'name' => Laminas\Log\Processor\RequestId::class,
            ],
        ],
    ],
    'logger' => [
        'AuditLogger' => [
            'writers'    => [
                'stream' => [
                    'name'     => 'stream',
                    'priority' => Laminas\Log\Logger::ALERT,
                    'options'  => [
                        'stream'    => __DIR__ . '/../../data/log/audit.log',
                        'formatter' => [
                            'name'    => Laminas\Log\Formatter\Simple::class,
                            'options' => [
                                'format'         => '%timestamp% %priorityName% (%priority%): %message% %extra%',
                                'dateTimeFormat' => 'c',
                            ],
                        ],
                        'filters'   => [
                            'priority' => [
                                'name'    => 'priority',
                                'options' => [
                                    'operator' => '<=',
                                    'priority' => Laminas\Log\Logger::INFO,
                                ],
                            ],
                        ],
                    ],
                ],
                'db'     => [
                    'name'     => 'db',
                    'priority' => Laminas\Log\Logger::ALERT,
                    'options'  => [
                        'db'        => new Laminas\Db\Adapter\Adapter([
                            'driver'   => getenv('DB_DRIVER'),
                            'database' => getenv('DB_DATABASE'),
                            'host'     => getenv('DB_HOSTNAME'),
                            'username' => getenv('DB_USER'),
                            'password' => getenv('DB_PASSWORD'),
                            'port'     => getenv('DB_PORT'),
                        ]),
                        'table'     => 'log_audit',
                        'column'    => [
                            'timestamp'    => 'timestamp',
                            'priority'     => 'priority',
                            'priorityName' => 'priorityName',
                            'message'      => 'message',
                            'extra'        => [
                                'extra' => 'extra',
                            ],
                        ],
                        'formatter' => [
                            'name'    => Laminas\Log\Formatter\Db::class,
                            'options' => [
                                'dateTimeFormat' => 'Y-m-d h:i:s',
                            ],
                        ],
                        'filters'   => [
                            'priority' => [
                                'name'    => 'priority',
                                'options' => [
                                    'operator' => '<=',
                                    'priority' => Laminas\Log\Logger::INFO,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'processors' => [
                'requestid' => [
                    'name' => Laminas\Log\Processor\RequestId::class,
                ],
            ],
        ],
    ],
];

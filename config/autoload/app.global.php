<?php

declare(strict_types=1);

return [
    'app' => [
        'municipality' => getenv('APP_MUNICIPALITY'),
        'phone'        => getenv('APP_PHONE'),
        'url'          => getenv('APP_URL'),
        'email'        => getenv('APP_EMAIL'),
        'data_policy'  => getenv('APP_DATA_POLICY'),
        'notification' => [
            'frequency' => (int)getenv('APP_NOTIFICATION_FREQUENCY'),
            'mail'      => [
                'testTo'   => getenv('APP_NOTIFICATION_MAIL_TESTTO'),
                'subject'  => getenv('APP_NOTIFICATION_MAIL_SUBJECT'),
                'replayTo' => getenv('APP_NOTIFICATION_MAIL_REPLAYTO'),
            ],
        ],
        'survey' => [
            'disable'  => getenv('APP_SURVEY_DISABLE'),
            'template' => getenv('APP_SURVEY_TEMPLATE'),
            'url'      => getenv('APP_SURVEY_URL'),
            'time'     => getenv('APP_SURVEY_TIME'), // UTC
            'mail'     => [
                'testTo'   => getenv('APP_SURVEY_MAIL_TESTTO'),
                'subject'  => getenv('APP_SURVEY_MAIL_SUBJECT'),
                'replayTo' => getenv('APP_SURVEY_MAIL_REPLAYTO'),
            ],
        ]
    ],
];

<?php

declare(strict_types=1);

return [
    'app' => [
        'municipality'        => getenv('APP_MUNICIPALITY'),
        'phone'               => getenv('APP_PHONE'),
        'url'                 => getenv('APP_URL'),
        'url_admin'           => getenv('APP_URL_ADMIN'),
        'email'               => getenv('APP_EMAIL'),
        'data_policy'         => getenv('APP_DATA_POLICY'),
        'company_name_part_1' => getenv('APP_COMPANY_NAME_PART_1'),
        'company_name_part_2' => getenv('APP_COMPANY_NAME_PART_2'),
        'company_full_info'   => getenv('APP_COMPANY_FULL_INFO'),
        'appointment'         => [
            'expired_time_day_is_plus' => getenv('APP_APPOINTMENT_EXPIRED_TIME_DAY_IS_PLUS'),
            'expired_time_hour'        => getenv('APP_APPOINTMENT_EXPIRED_TIME_HOUR'),
            'expired_time_min'         => getenv('APP_APPOINTMENT_EXPIRED_TIME_MIN'),
        ],
        'notification'        => [
            'frequency' => (int)getenv('APP_NOTIFICATION_FREQUENCY'),
            'mail'      => [
                'testTo'   => getenv('APP_NOTIFICATION_MAIL_TESTTO'),
                'subject'  => getenv('APP_NOTIFICATION_MAIL_SUBJECT'),
                'replayTo' => getenv('APP_NOTIFICATION_MAIL_REPLAYTO'),
            ],
        ],
        'ics' => [
            'name'        => getenv('APP_ICS_NAME'),
            'description' => getenv('APP_ICS_DESCRIPTION'),
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

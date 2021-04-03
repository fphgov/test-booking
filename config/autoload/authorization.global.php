<?php

declare(strict_types=1);

return [
    'mezzio-authorization-acl' => [
        'roles'     => [
            'guest'     => [],
            'voluntary' => ['guest'],
            'cs'        => ['voluntary'],
            'admin'     => ['voluntary', 'cs'],
            'developer' => ['voluntary', 'cs', 'admin'],
        ],
        'resources' => [
            'app.api.ping',
            'app.api.applicant.add',
            'app.api.applicant.cancellation',
            'app.api.options.get',
            'app.api.appointment.time.get',
            'app.api.appointment.get',
            'app.api.appointment.reservation',

            'admin.api.login',
            'admin.api.cache.clear',
            'admin.api.dashboard.get',
            'admin.api.dashboard.set',
            'admin.api.applicant.search',
            'admin.api.applicant.get',
            'admin.api.applicant.post',
            'admin.api.applicant.del',
            'admin.api.applicant.export',
            'admin.api.check.search',
            'admin.api.check.get',
            'admin.api.check.post',
            'admin.api.generate.appointment',
        ],
        'allow'     => [
            'guest'     => [
                'app.api.ping',
                'app.api.applicant.add',
                'app.api.applicant.cancellation',
                'app.api.options.get',
                'app.api.appointment.time.get',
                'app.api.appointment.get',
                'app.api.appointment.reservation',

                'admin.api.login',
            ],
            'voluntary' => [
                'admin.api.dashboard.get',
                'admin.api.check.search',
                'admin.api.check.get',
                'admin.api.check.post',
            ],
            'cs' => [
                'admin.api.applicant.search',
                'admin.api.applicant.get',
                'admin.api.applicant.post',
                'admin.api.applicant.del',
            ],
            'admin' => [
                'admin.api.dashboard.set',
                'admin.api.applicant.export',
            ],
            'developer' => [
                'admin.api.cache.clear',
                'admin.api.generate.appointment',
            ],
        ]
    ]
];

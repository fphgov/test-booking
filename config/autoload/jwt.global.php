<?php

declare(strict_types=1);

return [
    'jwt' => [
        'iss' => getenv('JWT_ISS'),
        'aud' => getenv('JWT_AUD'),
        'jti' => getenv('JWT_JTI'),
        'nbf' => getenv('JWT_NBF'),
        'exp' => getenv('JWT_EXP'),
        'auth' => [
            'secret' => getenv('JWT_SECRET'),
        ]
    ]
];

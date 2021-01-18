<?php

declare(strict_types=1);

return [
    'doctrine_encrypt' => [
        'sha_type'       => getenv('ENCRYPT_SHA_TYPE'),
        'encrypt_method' => getenv('ENCRYPT_ENCRYPT_METHOD'),
        'secret_key'     => getenv('ENCRYPT_SECRET_KEY'),
        'secret_iv'      => getenv('ENCRYPT_SECRET_IV'),
    ]
];

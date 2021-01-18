<?php

declare(strict_types=1);

return [
  'authentication' => [
      'pdo' => [
          'dsn'   => 'mysql:host='. getenv('DB_HOSTNAME') .';port='. getenv('DB_PORT') .';dbname='. getenv('DB_DATABASE') .';charset=utf8',
          'username' => getenv('DB_USER'),
          'password' => getenv('DB_PASSWORD'),
          'table' => 'users',
          'field' => [
              'identity' => 'email',
              'password' => 'password',
          ],
          'sql_get_roles' => 'SELECT role FROM users WHERE email = :identity',
          'sql_get_details' => 'SELECT id, firstname FROM users WHERE email = :identity',
      ],
      'redirect' => '/login',
  ],
  'password' => [
    'cost' => 12,
  ]
];

<?php

declare(strict_types=1);

use Mezzio\Cors\Configuration\ConfigurationInterface;

return [
    ConfigurationInterface::CONFIGURATION_IDENTIFIER => [
        'allowed_origins' => [ConfigurationInterface::ANY_ORIGIN],
        'allowed_headers' => ['Origin', 'X-Requested-With', 'Content-Type', 'Accept', 'Authorization'], // No custom headers allowed
        'allowed_max_age' => '3600', // 60 minutes
        'credentials_allowed' => false, // Disallow cookies
        'exposed_headers' => [], // No headers are exposed
    ],
];

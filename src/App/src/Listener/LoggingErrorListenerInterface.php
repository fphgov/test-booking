<?php

declare(strict_types=1);

namespace App\Listener;

use Laminas\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

interface LoggingErrorListenerInterface
{
    public function __construct(LoggerInterface $logger);

    public function __invoke(Throwable $error, ServerRequestInterface $request, ResponseInterface $response): void;
}

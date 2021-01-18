<?php

declare(strict_types=1);

namespace App\Listener;

use Laminas\Log\Logger;
use Laminas\Log\Processor\PsrPlaceholder;
use Laminas\Stratigility\Middleware\ErrorHandler;
use Psr\Container\ContainerInterface;

class LoggingErrorListenerFactory
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $callback
    ): ErrorHandler {
        $logger = $container->get(Logger::class);
        $logger->addProcessor(new PsrPlaceholder());

        $listener = new LoggingErrorListener($logger);

        $errorHandler = $callback();
        $errorHandler->attachListener($listener);

        return $errorHandler;
    }
}

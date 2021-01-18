<?php

declare(strict_types=1);

namespace App\Middleware;

use Laminas\Log\Logger;
use Laminas\Log\Processor\PsrPlaceholder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuditMiddleware implements MiddlewareInterface
{
    /** @var Logger */
    private $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function getLogger(): Logger
    {
        return $this->logger;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $this->logger->addProcessor(new PsrPlaceholder());

        return $handler->handle(
            $request->withAttribute(self::class, $this->logger)
        );
    }
}

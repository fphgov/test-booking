<?php

declare(strict_types=1);

namespace Jwt\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tuupola\Middleware\JwtAuthentication;

class JwtAuthMiddleware implements MiddlewareInterface
{
    /** @var JwtAuthentication */
    private $auth;

    public function __construct(JwtAuthentication $auth)
    {
        $this->auth = $auth;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        return $this->auth->process($request, $handler);
    }
}

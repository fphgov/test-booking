<?php

declare(strict_types=1);

namespace App\Handler\Tools;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function opcache_reset;
use function time;

final class ClearCacheHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        opcache_reset();

        return new JsonResponse(['ack' => time()]);
    }
}

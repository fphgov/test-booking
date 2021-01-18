<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Handler\TokenHandler;
use Laminas\Diactoros\Response\JsonResponse;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

use function json_decode;

class TokenHandlerTest extends TestCase
{
    public function testResponse()
    {
        $tokenHandler = new TokenHandler();
        $response     = $tokenHandler->handle(
            $this->prophesize(ServerRequestInterface::class)->reveal()
        );

        $json = json_decode((string) $response->getBody());

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertTrue(isset($json->ack));
    }
}

<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Handler\HomePageHandler;
use Laminas\Diactoros\Response\JsonResponse;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ServerRequestInterface;

class HomePageHandlerTest extends TestCase
{
    use ProphecyTrait;

    protected function setUp(): void
    {
    }

    public function testReturnsJsonResponseProvided()
    {
        $homePage        = new HomePageHandler();
        $serverRequester = $this->prophesize(ServerRequestInterface::class);

        $response = $homePage->handle(
            $serverRequester->reveal()
        );

        $this->assertInstanceOf(JsonResponse::class, $response);
    }
}

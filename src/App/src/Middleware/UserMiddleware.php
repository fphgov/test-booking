<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Service\UserServiceInterface;
use Jwt\Handler\JwtAuthMiddleware;
use Mezzio\Authentication\DefaultUser;
use Mezzio\Authentication\UserInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class UserMiddleware implements MiddlewareInterface
{
    /** @var UserServiceInterface */
    private $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $token = $request->getAttribute(JwtAuthMiddleware::class);

        $user = $this->userService->getRepository()->findOneBy([
            'email' => $token['user']->email,
        ]);

        $ui = new DefaultUser($user->getEmail(), [$user->getRole()]);

        return $handler->handle(
            $request
                ->withAttribute(self::class, $user)
                ->withAttribute(UserInterface::class, $ui)
        );
    }
}

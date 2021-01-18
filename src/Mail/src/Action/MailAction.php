<?php

declare(strict_types=1);

namespace Mail\Action;

use Laminas\Mail\Transport\Smtp;
use Mail\MailAdapter;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MailAction implements MiddlewareInterface
{
    /** @var MailAdapter */
    private $mailAdapter;

    public function __construct(
        Smtp $transport,
        array $config,
        ?TemplateRendererInterface $template = null
    ) {
        $this->mailAdapter = new MailAdapter($transport, $config, $template);
    }

    public function getAdapter(): MailAdapter
    {
        return $this->mailAdapter;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        return $handler->handle(
            $request->withAttribute(self::class, $this->mailAdapter)
        );
    }
}

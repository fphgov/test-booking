<?php

declare(strict_types=1);

namespace App\Service;

use App\Middleware\AuditMiddleware;
use Doctrine\ORM\EntityManagerInterface;
use Interop\Container\ContainerInterface;
use Mail\Action\MailAction;
use Mezzio\LaminasView\LaminasViewRenderer;
use Pdf\Interfaces\PdfRender;

final class NoticeServiceFactory
{
    public function __invoke(ContainerInterface $container): NoticeService
    {
        $config = $container->has('config') ? $container->get('config') : [];

        $template = $container->has(LaminasViewRenderer::class)
            ? $container->get(LaminasViewRenderer::class)
            : null;

        return new NoticeService(
            $config,
            $container->get(EntityManagerInterface::class),
            $container->get(PdfRender::class),
            $container->get(MailAction::class)->getAdapter(),
            $container->get(AuditMiddleware::class)->getLogger(),
            $template
        );
    }
}

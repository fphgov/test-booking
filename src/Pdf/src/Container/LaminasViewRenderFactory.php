<?php

declare(strict_types=1);

namespace Pdf\Container;

use Interop\Container\ContainerInterface;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\View\Resolver;
use Pdf\Interfaces\PdfRender;

class LaminasViewRenderFactory
{
    public function __invoke(ContainerInterface $container): PdfRender
    {
        $renderer = new PhpRenderer();

        $resolver = new Resolver\AggregateResolver();
        $resolver->attach(
            (new Resolver\TemplatePathStack())->setPaths([__DIR__ . '/../templates'])
        );

        $renderer->setResolver($resolver);

        return new PdfRender($renderer);
    }
}

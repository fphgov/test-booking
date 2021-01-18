<?php

declare(strict_types=1);

namespace App\Middleware;

use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Middlewares\Recaptcha;
use Psr\Container\ContainerInterface;

class RecaptchaMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): Recaptcha
    {
        $config = $container->has('config') ? $container->get('config') : [];

        if (! isset($config['recaptcha']) || ! isset($config['recaptcha']['secret'])) {
            throw new ServiceNotCreatedException('Missing recaptcha configuration');
        }

        return new Recaptcha($config['recaptcha']['secret']);
    }
}

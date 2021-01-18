<?php

declare(strict_types=1);

namespace Mail\Container;

use Interop\Container\ContainerInterface;
use Laminas\Mail\Transport\Smtp;
use Laminas\Mail\Transport\SmtpOptions;
use Laminas\Mail\Transport\TransportInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;

class SmtpFactory
{
    public function __invoke(ContainerInterface $container): TransportInterface
    {
        $config = $container->has('config') ? $container->get('config') : [];

        if (! isset($config['mail']) || ! isset($config['mail']['smtp'])) {
            throw new ServiceNotCreatedException('Missing Mail SMTP configuration');
        }

        $conf = $config['mail']['smtp'];

        $transport = new Smtp();
        $options   = new SmtpOptions($conf);

        $transport->setOptions($options);

        return $transport;
    }
}

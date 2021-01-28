<?php

declare(strict_types=1);

opcache_invalidate(__FILE__, true);

if (PHP_SAPI !== 'cli') {
    return false;
}

chdir(__DIR__ . '/../../');

use Doctrine\ORM\EntityManagerInterface;
use Mail\Action\MailAction;

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createUnsafeMutable(dirname(__DIR__, 2));
$dotenv->load();

$config = include 'config/config.php';
$container = require 'config/container.php';

$em = $container->get(EntityManagerInterface::class);
$mailAdapter = $container->get(MailAction::class)->getAdapter();

$mailAdapter->message->addTo($config['app']['notification']['mail']['testTo']);
$mailAdapter->message->setSubject($config['app']['notification']['mail']['subject']);
$mailAdapter->message->addReplyTo($config['app']['notification']['mail']['replayTo']);

$tplData = [
    'name'       => 'Tamás',
    'humanID'    => 'F1-0001',
    'time'       => 'F1-0001',
    'place'      => 'Budapest',
    'placeLink'  => 'http://www.google.com/maps/place/47.49685621188065,19.055092671651998',
    'cancelHash' => 'VmRPNjllU0RwOFNY',

    'infoMunicipality'     => $config['app']['municipality'],
    'infoPhone'            => $config['app']['phone'],
    'infoEmail'            => $config['app']['email'],
    'infoUrl'              => $config['app']['url'],
    'infoDataPolicy'       => $config['app']['data_policy'],
    'infoCompanyNamePart1' => $config['app']['company_name_part_1'],
    'infoCompanyNamePart2' => $config['app']['company_name_part_2'],
    'infoCompanyFullInfo'  => $config['app']['company_full_info'],
];

$mailAdapter->setTemplate('email/created', $tplData)->send();

<?php

declare(strict_types=1);

opcache_invalidate(__FILE__, true);

if (PHP_SAPI !== 'cli') {
    return false;
}

chdir(__DIR__ . '/../../');

use App\Entity\Applicant;
use App\Entity\Appointment;
use App\Service\NoticeServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createUnsafeMutable(dirname(__DIR__, 2));
$dotenv->load();

$config = include 'config/config.php';
$container = require 'config/container.php';

$em            = $container->get(EntityManagerInterface::class);
$noticeService = $container->get(NoticeServiceInterface::class);

$applicantRepository = $em->getRepository(Applicant::class);

$applicants = $applicantRepository->findBy([
    'notified' => false,
], [], (int) $config['app']['notification']['frequency']);

foreach($applicants as $applicant) {
    try {
        $noticeService->sendEmail($applicant);
        sleep(1);
    } catch (\Throwable $th) {
        error_log($applicant->getHumanId());
    }
}

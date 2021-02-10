<?php

declare(strict_types=1);

opcache_invalidate(__FILE__, true);

if (PHP_SAPI !== 'cli') {
    return false;
}

chdir(__DIR__ . '/../../');

use App\Entity\Applicant;
use App\Entity\Appointment;
use Dompdf\Dompdf;
use Doctrine\ORM\EntityManagerInterface;
use Pdf\Interfaces\PdfRender;
use Mail\Action\MailAction;

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createUnsafeMutable(dirname(__DIR__, 2));
$dotenv->load();

$config = include 'config/config.php';
$container = require 'config/container.php';

if ($config['app']['survey']['disable']) {
    exit();
}

$em = $container->get(EntityManagerInterface::class);
$applicantRepository = $em->getRepository(Applicant::class);
$mailAdapter = $container->get(MailAction::class)->getAdapter();

$times = explode(':', $config['app']['survey']['time']);

$today = new DateTime();

$endOfDay = clone $today;
$endOfDay->setTime((int)$times[0], (int)$times[1]);

if ($today < $endOfDay) {
    exit();
}

$applicants = $applicantRepository->getApplicantsByDate($today, 20);

foreach($applicants as $applicant) {
    try {
        $mailAdapter->clear();

        $mailAdapter->message->addTo($applicant->getEmail());
        $mailAdapter->message->setSubject($config['app']['survey']['mail']['subject']);
        $mailAdapter->message->addReplyTo($config['app']['survey']['mail']['replayTo']);

        $tplData = [
            'name'             => $applicant->getLastname() . ' ' . $applicant->getFirstname(),
            'url'              => $config['app']['survey']['url'],
            'infoMunicipality' => $config['app']['municipality'],
        ];

        $mailAdapter->setTemplate($config['app']['survey']['template'], $tplData)->send();

        $applicant->setSurvey(true);
        $em->flush();

        sleep(1);
    } catch (\Throwable $th) {
        error_log($applicant->getHumanId());
    }
}

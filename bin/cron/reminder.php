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

$config    = include 'config/config.php';
$container = require 'config/container.php';

if ($config['app']['reminder']['disable']) {
    exit();
}

$em                  = $container->get(EntityManagerInterface::class);
$applicantRepository = $em->getRepository(Applicant::class);
$mailAdapter         = $container->get(MailAction::class)->getAdapter();

$endOfDay = new DateTime();
$endOfDay->add(new DateInterval('P1D'));

$applicants = $applicantRepository->getApplicantsToReminder($endOfDay, 20);

foreach($applicants as $applicant) {
    try {
        $mailAdapter->clear();

        $mailAdapter->message->addTo($applicant->getEmail());
        $mailAdapter->message->setSubject($config['app']['reminder']['mail']['subject']);

        $tplData = [
            'name'             => $applicant->getFirstname(),
            'cancelHash'       => $applicant->getCancelHash(),
            'infoUrl'          => $config['app']['url'],
            'infoMunicipality' => $config['app']['municipality'],
        ];

        $mailAdapter->setTemplate($config['app']['reminder']['template'], $tplData)->send();

        $applicant->setReminder(true);
        $em->flush();

        sleep(1);
    } catch (\Throwable $th) {
        error_log($applicant->getHumanId());
    }
}

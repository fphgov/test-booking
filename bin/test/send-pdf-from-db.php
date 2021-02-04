<?php

declare(strict_types=1);

opcache_invalidate(__FILE__, true);

if (PHP_SAPI !== 'cli') {
    return false;
}

chdir(__DIR__ . '/../../');

use App\Entity\Applicant;
use App\Entity\Appointment;
use App\Service\NoticeService;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Dompdf\Dompdf;
use Doctrine\ORM\EntityManagerInterface;
use Pdf\Interfaces\PdfRender;
use Mail\Action\MailAction;

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createUnsafeMutable(dirname(__DIR__, 2));
$dotenv->load();

$config = include 'config/config.php';
$container = require 'config/container.php';

$qrOptions = new QROptions([
    'version'      => 8,
    'outputType'   => QRCode::OUTPUT_MARKUP_SVG,
    'eccLevel'     => QRCode::ECC_H,
    'addQuietzone' => false,
]);

$em = $container->get(EntityManagerInterface::class);
$template = $container->get(PdfRender::class);
$applicantRepository = $em->getRepository(Applicant::class);

$dompdf = new Dompdf();

$applicant = $applicantRepository->find(12);
$appointment = $applicant->getAppointment();

$qrData = NoticeService::addHttp($config['app']['url_admin'] . '#/checks/' . $humanId);

$tplData = [
    'fullName'   => $applicant->getLastname() . ' ' . $applicant->getFirstname(),
    'humanID'    => $applicant->getHumanId(),
    'time'       => $appointment->getDate()->format('Y.m.d. H.i'),
    'place'      => $appointment->getPlace()->getName(),
    'address'    => $applicant->getAddress(),
    'taj'        => $applicant->getTaj(),
    'phone'      => $applicant->getPhone(),
    'email'      => $applicant->getEmail(),
    'birthPlace' => $applicant->getBirthdayPlace(),
    'birthday'   => $applicant->getBirthday()->format('Y.m.d.'),
    'signDate'   => $appointment->getDate()->format('Y.m.d.'),

    'infoMunicipality'     => $config['app']['municipality'],
    'infoPhone'            => $config['app']['phone'],
    'infoEmail'            => $config['app']['email'],
    'infoUrl'              => $config['app']['url'],
    'infoDataPolicy'       => $config['app']['data_policy'],
    'infoCompanyNamePart1' => $config['app']['company_name_part_1'],
    'infoCompanyNamePart2' => $config['app']['company_name_part_2'],
    'infoCompanyFullInfo'  => $config['app']['company_full_info'],

    'qrCode' => (new QRCode($qrOptions))->render($qrData),
];

$dompdf = new Dompdf();
$dompdf->loadHtml($template->render('pdf/created', $tplData));
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$pdf = $dompdf->output();

$dompdf = new Dompdf();
$dompdf->loadHtml($template->render('pdf/result', $tplData));
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$resultPdf = $dompdf->output();

$mailAdapter = $container->get(MailAction::class)->getAdapter();

$mailAdapter->message->addTo($applicant->getEmail());
$mailAdapter->message->setSubject($config['app']['notification']['mail']['subject']);
$mailAdapter->message->addReplyTo($config['app']['notification']['mail']['replayTo']);

$emailTplData = [
    'name'       => $applicant->getFirstname(),
    'humanID'    => $applicant->getHumanId(),
    'time'       => $appointment->getDate()->format('Y.m.d. H.i'),
    'place'      => $appointment->getPlace()->getDescription(),
    'placeLink'  => $appointment->getPlace()->getLink(),
    'cancelHash' => $applicant->getCancelHash(),
    'infoUrl'    => $config['app']['url'],

    'infoMunicipality' => $config['app']['municipality'],
    'infoPhone'        => $config['app']['phone'],
    'infoEmail'        => $config['app']['email'],
    'infoUrl'          => $config['app']['url'],
    'infoDataPolicy'   => $config['app']['data_policy'],
];

$mailAdapter->setTemplate('email/created', $emailTplData)
    ->addPdfAttachment($tplData['humanID'] . '_regisztracio_igazolas.pdf', $pdf)
    ->addPdfAttachment($tplData['humanID'] . '_regisztracio_eredmeny.pdf', $resultPdf)
    ->send();

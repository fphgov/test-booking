<?php

declare(strict_types=1);

opcache_invalidate(__FILE__, true);

if (PHP_SAPI !== 'cli') {
    return false;
}

chdir(__DIR__ . '/../../');

use App\Entity\Applicant;
use App\Entity\Appointment;
use App\Entity\Place;
use App\Service\NoticeService;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Dompdf\Dompdf;
use Dompdf\Options;
use Pdf\Interfaces\PdfRender;
use Mail\Action\MailAction;

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createUnsafeMutable(dirname(__DIR__, 2));
$dotenv->load();

$config    = include 'config/config.php';
$container = require 'config/container.php';

$template = $container->get(PdfRender::class);

$qrOptions = new QROptions([
    'version'      => 8,
    'outputType'   => QRCode::OUTPUT_MARKUP_SVG,
    'eccLevel'     => QRCode::ECC_H,
    'addQuietzone' => false,
]);

$place = new Place();
$place->setId(1);
$place->setName('Árpád magyar fejedelem útja 99, XXIII. kerület');
$place->setShortName('A1');
$place->setLink('http://www.google.com/maps/place/47.49685621188065,19.055092671651998');
$place->setDescription('Kelenföldi pályaudvar – Etele tér (metrókijáró előtti park a Volánbusz pályaudvar és a BKV buszvégállomás között)');

$appointment = new Appointment();
$appointment->setId(1);
$appointment->setDate(new DateTime('2020-12-14 08:00:00'));
$appointment->setPlace($place);
$appointment->setPhase(0);

$applicant = new Applicant();
$applicant->setId(1);
$applicant->setAppointment($appointment);
$applicant->setCancelHash('VmRPNjllU0RwOFNY');
$applicant->setLastname('Kovács');
$applicant->setFirstname('János');
$applicant->setHumanId('F1-00001');
$applicant->setAddress('1052 Budapest, Városház utca 0');
$applicant->setTaj('111 222 333');
$applicant->setPhone('36300001122');
$applicant->setEmail('john.smith@test.hu');
$applicant->setBirthdayPlace('Budapest');
$applicant->setBirthday(new DateTime('1990-01-01'));

$qrData = NoticeService::addHttp($config['app']['url_admin'] . '#/checks/' . $applicant->getHumanId());

$tplData = [
    'fullName'   => $applicant->getLastname() . ' ' . $applicant->getFirstname(),
    'humanID'    => $applicant->getHumanId(),
    'time'       => $applicant->getAppointment()->getDate()->format('Y.m.d. H.i'),
    'place'      => $applicant->getAppointment()->getPlace()->getName(),
    'address'    => $applicant->getAddress(),
    'taj'        => $applicant->getTaj(),
    'phone'      => $applicant->getPhone(),
    'email'      => $applicant->getEmail(),
    'birthPlace' => $applicant->getBirthdayPlace(),
    'birthday'   => $applicant->getBirthday()->format('Y.m.d.'),
    'signDate'   => $applicant->getAppointment()->getDate()->format('Y.m.d.'),

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

$mailAdapter->message->addTo($config['app']['notification']['mail']['testTo']);
$mailAdapter->message->setSubject($config['app']['notification']['mail']['subject']);
$mailAdapter->message->addReplyTo($config['app']['notification']['mail']['replayTo']);

$emailTplData = [
    'name'       => $applicant->getFirstname(),
    'humanID'    => $applicant->getHumanId(),
    'time'       => $applicant->getAppointment()->getDate()->format('Y.m.d. H.i'),
    'place'      => $applicant->getAppointment()->getPlace()->getDescription(),
    'placeLink'  => $applicant->getAppointment()->getPlace()->getLink(),
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

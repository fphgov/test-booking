<?php

declare(strict_types=1);

opcache_invalidate(__FILE__, true);

if (PHP_SAPI !== 'cli') {
    return false;
}

chdir(__DIR__ . '/../../');

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

$config = include 'config/config.php';
$container = require 'config/container.php';

$qrOptions = new QROptions([
    'version'      => 8,
    'outputType'   => QRCode::OUTPUT_MARKUP_SVG,
    'eccLevel'     => QRCode::ECC_H,
    'addQuietzone' => false,
]);

$template = $container->get(PdfRender::class);

$humanId = 'F1-00001';

$qrData = NoticeService::addHttp($config['app']['url_admin'] . '#/checks/' . $humanId);

$tplData = [
    'fullName'   => 'Kovács János',
    'humanID'    => $humanId,
    'time'       => '2020.12.14 8.00',
    'place'      => 'Etele tér, XI. kerület',
    'place'      => 'Árpád magyar fejedelem útja 99, XXIII. kerület',
    'address'    => '1052 Budapest, Városház utca 0',
    'taj'        => '111 222 333',
    'phone'      => '36300001122',
    'email'      => 'john.smith@test.hu',
    'birthPlace' => 'Budapest',
    'birthday'   => '1990.01.01.',
    'signDate'   => '2020.12.14.',

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
    'name'       => 'John',
    'humanID'    => $humanId,
    'time'       => '2020.12.14 8.00',
    'place'      => 'Kelenföldi pályaudvar – Etele tér (metrókijáró előtti park a Volánbusz pályaudvar és a BKV buszvégállomás között)',
    'placeLink'  => 'http://www.google.com/maps/place/47.49685621188065,19.055092671651998',
    'cancelHash' => 'VmRPNjllU0RwOFNY',
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

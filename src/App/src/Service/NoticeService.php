<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ApplicantInterface;
use App\Entity\AppointmentInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Laminas\Log\Logger;
use Mail\MailAdapter;
use Mezzio\Template\TemplateRendererInterface;
use Pdf\Interfaces\PdfRender;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Throwable;

use function preg_match;
use function strip_tags;

final class NoticeService implements NoticeServiceInterface
{
    /** @var array */
    private $config;

    /** @var EntityManagerInterface */
    private $em;

    /** @var PdfRender */
    private $pdfRender;

    /** @var MailAdapter */
    private $mailAdapter;

    /** @var TemplateRendererInterface */
    private $template;

    /** @var Logger */
    private $audit;

    public function __construct(
        array $config,
        EntityManagerInterface $em,
        PdfRender $pdfRender,
        MailAdapter $mailAdapter,
        Logger $audit,
        ?TemplateRendererInterface $template = null
    ) {
        $this->config      = $config;
        $this->em          = $em;
        $this->pdfRender   = $pdfRender;
        $this->mailAdapter = $mailAdapter;
        $this->audit       = $audit;
        $this->template    = $template;
    }

    public function sendEmail(ApplicantInterface $applicant): void
    {
        $this->mailAdapter->clear();

        $appointment = $applicant->getAppointment();

        try {
            $this->mailAdapter->message->addTo($applicant->getEmail());
            $this->mailAdapter->message->setSubject($this->config['app']['notification']['mail']['subject']);
            $this->mailAdapter->message->addReplyTo($this->config['app']['notification']['mail']['replayTo']);

            $tplData = [
                'name'                 => $applicant->getFirstname(),
                'humanID'              => $applicant->getHumanId(),
                'time'                 => $appointment->getDate()->format('Y.m.d. H.i'),
                'place'                => $appointment->getPlace()->getDescription(),
                'placeLink'            => $appointment->getPlace()->getLink(),
                'cancelHash'           => $applicant->getCancelHash(),
                'infoMunicipality'     => $this->config['app']['municipality'],
                'infoPhone'            => $this->config['app']['phone'],
                'infoEmail'            => $this->config['app']['email'],
                'infoUrl'              => $this->config['app']['url'],
                'infoDataPolicy'       => $this->config['app']['data_policy'],
                'infoCompanyNamePart1' => $this->config['app']['company_name_part_1'],
                'infoCompanyNamePart2' => $this->config['app']['company_name_part_2'],
                'infoCompanyFullInfo'  => $this->config['app']['company_full_info'],
            ];

            $pdf       = $this->getPdf($applicant, $appointment, 'pdf/created');
            $resultPdf = $this->getPdf($applicant, $appointment, 'pdf/result');

            $description = $this->config['app']['ics']['description'];

            if ($this->template !== null) {
                $description = strip_tags($this->template->render('email/created', $tplData));
            }

            $ics = $this->getCalendar($applicant, $description);

            $this->mailAdapter
                ->setTemplate('email/created', $tplData)
                ->addPdfAttachment($applicant->getHumanId() . '_regisztracio_igazolas.pdf', $pdf)
                ->addPdfAttachment($applicant->getHumanId() . '_regisztracio_eredmeny.pdf', $resultPdf)
                ->addIcsAttachment($applicant->getHumanId() . '.ics', $ics)
                ->send();

            $applicant->setNotified(true);

            $this->em->flush();
        } catch (Throwable $e) {
            $this->audit->err('Mail no sended new applicant', [
                'extra' => $applicant->getHumanId(),
            ]);
        }
    }

    private function getQRCode(string $qrData): string
    {
        $qrOptions = new QROptions([
            'version'      => 8,
            'outputType'   => QRCode::OUTPUT_MARKUP_SVG,
            'eccLevel'     => QRCode::ECC_H,
            'addQuietzone' => false,
        ]);

        return (new QRCode($qrOptions))->render($qrData);
    }

    private function getPdf(ApplicantInterface $applicant, AppointmentInterface $appointment, string $template): ?string
    {
        $dompdf = new Dompdf();

        $qrData = self::addHttp($this->config['app']['url_admin'] . '#/checks/' . $applicant->getHumanId());

        $tplData = [
            'fullName'             => $applicant->getLastname() . ' ' . $applicant->getFirstname(),
            'humanID'              => $applicant->getHumanId(),
            'time'                 => $appointment->getDate()->format('Y.m.d. H.i'),
            'place'                => $appointment->getPlace()->getName(),
            'address'              => $applicant->getAddress(),
            'taj'                  => $applicant->getTaj(),
            'phone'                => $applicant->getPhone(),
            'email'                => $applicant->getEmail(),
            'birthPlace'           => $applicant->getBirthdayPlace(),
            'birthday'             => $applicant->getBirthday()->format('Y.m.d.'),
            'signDate'             => $appointment->getDate()->format('Y.m.d.'),
            'infoCompanyNamePart1' => $this->config['app']['company_name_part_1'],
            'infoCompanyNamePart2' => $this->config['app']['company_name_part_2'],
            'infoCompanyFullInfo'  => $this->config['app']['company_full_info'],
            'qrCode'               => $this->getQRCode($qrData),
        ];

        $dompdf->loadHtml($this->pdfRender->render($template, $tplData));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();

        unset($dompdf);

        return $output;
    }

    private function getCalendar(ApplicantInterface $applicant, string $description): string
    {
        $appointment = $applicant->getAppointment();

        $start = $appointment->getDate();

        $end = clone $start;
        $end->modify('+15 minutes');

        $event = Event::create()
                ->name($this->config['app']['ics']['name'])
                ->description($description)
                ->organizer($this->config['app']['email'], $this->config['app']['municipality'])
                ->url($this->config['app']['url'])
                ->uniqueIdentifier($applicant->getHumanId())
                ->startsAt($start)
                ->endsAt($end)
                ->withoutTimezone()
                ->address($appointment->getPlace()->getName());

        $calendar = Calendar::create();
        $calendar->event($event);

        return $calendar->get();
    }

    public static function addHttp(string $url): string
    {
        if (! preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url;
        }

        return $url;
    }
}

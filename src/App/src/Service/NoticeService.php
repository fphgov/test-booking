<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ApplicantInterface;
use App\Entity\AppointmentInterface;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Laminas\Log\Logger;
use Mail\MailAdapter;
use Pdf\Interfaces\PdfRender;
use Throwable;

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

    /** @var Logger */
    private $audit;

    public function __construct(
        array $config,
        EntityManagerInterface $em,
        PdfRender $pdfRender,
        MailAdapter $mailAdapter,
        Logger $audit
    ) {
        $this->config      = $config;
        $this->em          = $em;
        $this->pdfRender   = $pdfRender;
        $this->mailAdapter = $mailAdapter;
        $this->audit       = $audit;
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
                'name'             => $applicant->getFirstname(),
                'humanID'          => $applicant->getHumanId(),
                'time'             => $appointment->getDate()->format('Y.m.d. H.i'),
                'place'            => $appointment->getPlace()->getDescription(),
                'placeLink'        => $appointment->getPlace()->getLink(),
                'cancelHash'       => $applicant->getCancelHash(),
                'infoMunicipality' => $this->config['app']['municipality'],
                'infoPhone'        => $this->config['app']['phone'],
                'infoEmail'        => $this->config['app']['email'],
                'infoUrl'          => $this->config['app']['url'],
                'infoDataPolicy'   => $this->config['app']['data_policy'],
            ];

            $pdf       = $this->getPdf($applicant, $appointment, 'pdf/created');
            $resultPdf = $this->getPdf($applicant, $appointment, 'pdf/result');

            $this->mailAdapter
                ->setTemplate('email/created', $tplData)
                ->addPdfAttachment($applicant->getHumanId() . '_regisztracio_igazolas.pdf', $pdf)
                ->addPdfAttachment($applicant->getHumanId() . '_regisztracio_eredmeny.pdf', $resultPdf)
                ->send();

            $applicant->setNotified(true);

            $this->em->flush();
        } catch (Throwable $e) {
            $this->audit->err('Mail no sended new applicant', [
                'extra' => $applicant->getHumanId(),
            ]);
        }
    }

    private function getPdf(ApplicantInterface $applicant, AppointmentInterface $appointment, string $template): ?string
    {
        $dompdf = new Dompdf();

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
        ];

        $dompdf->loadHtml($this->pdfRender->render($template, $tplData));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();

        unset($dompdf);

        return $output;
    }
}

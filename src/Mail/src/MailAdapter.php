<?php

declare(strict_types=1);

namespace Mail;

use Laminas\Mail\Header;
use Laminas\Mail\Message;
use Laminas\Mail\Transport\Smtp;
use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Mime;
use Laminas\Mime\Part;
use Mezzio\Template\TemplateRendererInterface;
use Throwable;

use function base_convert;
use function bin2hex;
use function error_log;
use function is_array;
use function microtime;
use function openssl_random_pseudo_bytes;
use function strip_tags;

class MailAdapter
{
    /** @var array */
    private $config;

    /** @var Smtp */
    private $transport;

    /** @var TemplateRendererInterface */
    private $template;

    /** @var Message */
    public $message;

    public function __construct(
        Smtp $transport,
        array $config,
        ?TemplateRendererInterface $template = null
    ) {
        $this->transport = $transport;
        $this->template  = $template;
        $this->config    = $config;

        $this->clear();
    }

    public function setTemplate(string $name, array $data): self
    {
        if ($this->template !== null) {
            $html     = $this->template->render($name, $data);
            $bodyPart = new MimeMessage();

            $bodyHtml           = new Part($html);
            $bodyHtml->type     = Mime::TYPE_HTML;
            $bodyHtml->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
            $bodyHtml->charset  = 'utf-8';

            $bodyPart->setParts([$bodyHtml]);

            $this->message->setBody($bodyPart);
            $this->message->setEncoding('UTF-8');
        }

        return $this;
    }

    public function addPdfAttachment(string $filename, string $stream): self
    {
        $pdf              = new Part($stream);
        $pdf->type        = 'application/pdf';
        $pdf->filename    = $filename;
        $pdf->disposition = Mime::DISPOSITION_ATTACHMENT;
        $pdf->encoding    = Mime::ENCODING_BASE64;

        $body = $this->message->getBody();

        if ($body instanceof MimeMessage) {
            $body->addPart($pdf);

            $this->message->setBody($body);
        }

        return $this;
    }

    public function addIcsAttachment(string $filename, string $stream): self
    {
        $ics              = new Part($stream);
        $ics->type        = 'text/calendar';
        $ics->filename    = $filename;
        $ics->disposition = Mime::DISPOSITION_ATTACHMENT;
        $ics->encoding    = Mime::ENCODING_BASE64;

        $body = $this->message->getBody();

        if ($body instanceof MimeMessage) {
            $body->addPart($ics);

            $this->message->setBody($body);
        }

        return $this;
    }

    public function send(): void
    {
        $this->transport->send($this->message);
    }

    public function clear(): void
    {
        $this->message = new Message();

        if (is_array($this->config['defaults'])) {
            foreach ($this->config['defaults'] as $ck => $cv) {
                try {
                    $this->message->{$ck}($cv);
                } catch (Throwable $e) {
                    error_log($e->getMessage());
                }
            }
        }

        $this->setMessageId();
    }

    private function setMessageId(): void
    {
        $key  = base_convert(microtime(), 10, 36) . base_convert(bin2hex(openssl_random_pseudo_bytes(8)), 16, 36);
        $key .= '@' . $this->config['headers']['message_id_domain'];

        $messageId = Header\MessageId::fromString('message-id: ' . $key);

        $this->message->getHeaders()->removeHeader($messageId);
        $this->message->getHeaders()->addHeader($messageId);
    }
}

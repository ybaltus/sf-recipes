<?php

namespace App\Services;

use App\Entity\Contact;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class MailService
{
    public function __construct(
        private MailerInterface $mailer
    )
    {
    }

    public function sendEmail(
        string $from,
        string $to,
        string $subject,
        string $htmlTemplate,
        array $context,
    ): void
    {
        $email = (new TemplatedEmail())
            ->from($from)
            ->to('admin@sf6recipe.fr')
            ->subject($subject)
            // path of the Twig template to render
            ->htmlTemplate($htmlTemplate)
            // pass variables (name => value) to the template
            ->context($context);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            // some error prevented the email sending; display an
            // error message or try to resend the message
            var_dump($e->getMessage());
        }
    }
}
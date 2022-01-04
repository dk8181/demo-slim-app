<?php

declare(strict_types=1);

namespace App\Auth\Service;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as MimeEmail;

class JoinConfirmationSender
{
    private MailerInterface $mailer;
    private string $from;

    public function __construct(MailerInterface $mailer, string $from)
    {
        $this->mailer = $mailer;
        $this->from = $from;
    }

    public function send(Email $email, Token $token): void
    {
        /** @var MimeEmail $mimeEmail */
        $mimeEmail = (new MimeEmail())
            ->from($this->from)
            ->to($email->getValue())
            ->subject('Your confirm token')
            ->priority(MimeEmail::PRIORITY_HIGH)
            ->html(
                '<h2>This is your validation link</h2>'
                . '<p>'
                . '<a href="/join/confirm?'
                . \http_build_query([
                    'token' => $token->getValue(),
                ])
                . '">'
                . 'Click for join'
                . '</a>'
                . '</p>'
            )
        ;

        $this->mailer->send($mimeEmail);
    }
}

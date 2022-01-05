<?php

declare(strict_types=1);

namespace App\Auth\Service;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use App\Frontend\FrontendUrlGenerator;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as MimeEmail;

class JoinConfirmationSender
{
    public const JOIN_URI = '/join/confirm';

    private MailerInterface $mailer;
    private FrontendUrlGenerator $frontend;
    private string $from;

    public function __construct(
        MailerInterface $mailer,
        FrontendUrlGenerator $frontend,
        string $from
    ) {
        $this->mailer = $mailer;
        $this->frontend = $frontend;
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
                . '<a href="'
                . $this
                    ->frontend
                    ->generate(
                        self::JOIN_URI,
                        [
                            'token' => $token->getValue(),
                        ]
                    )
                . '">'
                . 'Click for join'
                . '</a>'
                . '</p>'
            )
        ;

        $this->mailer->send($mimeEmail);
    }
}

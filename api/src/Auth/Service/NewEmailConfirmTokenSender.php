<?php

declare(strict_types=1);

namespace App\Auth\Service;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as MimeEmail;
use Twig\Environment;

class NewEmailConfirmTokenSender
{
    public const URI = 'email/confirm';
    public const SUBJECT = 'Your confirmation of setting new email';
    public const TEMPLATE_PATH = 'auth/email/confirm.html.twig';

    private MailerInterface $mailer;
    private Environment $twig;
    private string $from;

    public function __construct(
        MailerInterface $mailer,
        Environment $twig,
        string $from
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->from = $from;
    }

    public function send(Email $email, Token $token): void
    {
        /** @var MimeEmail $mimeEmail */
        $mimeEmail = (new MimeEmail())
            ->from($this->from)
            ->to($email->getValue())
            ->subject(self::SUBJECT)
            ->priority(MimeEmail::PRIORITY_HIGH)
            ->html(
                $this->twig->render(
                    self::TEMPLATE_PATH,
                    [
                        'uri' => self::URI,
                        'token' => $token,
                    ]
                )
            )
        ;

        $this->mailer->send($mimeEmail);
    }
}

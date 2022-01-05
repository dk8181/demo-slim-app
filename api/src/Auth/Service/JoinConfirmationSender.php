<?php

declare(strict_types=1);

namespace App\Auth\Service;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use App\Frontend\FrontendUrlGenerator;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as MimeEmail;
use Twig\Environment;

class JoinConfirmationSender
{
    public const JOIN_URI = '/join/confirm';

    private MailerInterface $mailer;
    private Environment $twig;
    private FrontendUrlGenerator $frontend;
    private string $from;

    public function __construct(
        MailerInterface $mailer,
        Environment $twig,
        FrontendUrlGenerator $frontend,
        string $from
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->frontend = $frontend;
        $this->from = $from;
    }

    public function send(Email $email, Token $token): void
    {
        /** @var MimeEmail $mimeEmail */
        $mimeEmail = (new MimeEmail())
            ->from($this->from)
            ->to($email->getValue())
            ->subject('Your confirmation of join')
            ->priority(MimeEmail::PRIORITY_HIGH)
            ->html(
                $this->twig->render(
                    'auth/join/confirm.html.twig',
                    [
                        'url' => $this->frontend->generate(
                            self::JOIN_URI,
                            [
                                'token' => $token->getValue(),
                            ]
                        ),
                    ]
                )
            )
        ;

        $this->mailer->send($mimeEmail);
    }
}

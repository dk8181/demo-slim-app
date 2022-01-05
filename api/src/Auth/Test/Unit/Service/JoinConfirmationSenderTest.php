<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Service;

use Ramsey\Uuid\Uuid;
use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use App\Auth\Service\JoinConfirmationSender;
use App\Frontend\FrontendUrlGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mime\Email as MimeEmail;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Address;
use Twig\Environment;

/**
 * @covers JoinConfirmationSender
 */
class JoinConfirmationSenderTest extends TestCase
{
    public function testSuccess(): void
    {
        $from = new Address('tester@app.test');
        $to = new Email('user@app.test');
        $token = new Token(Uuid::uuid4()->toString(), new \DateTimeImmutable());

        $confirmUrl = 'http://test.org/' . JoinConfirmationSender::JOIN_URI . '?token=' . $token->getValue();

        $twig = $this->createMock(Environment::class);

        /** \PHPUnit\Framework\MockObject\MockObject $twig */
        $twig
            ->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('auth/join/confirm.html.twig'),
                $this->equalTo([
                    'uri' => JoinConfirmationSender::JOIN_URI,
                    'token' => $token
                ])
            )
            ->willReturn($body = '<a href="' . $confirmUrl . '">' . $confirmUrl . '</a>')
        ;

        $mailer = $this->createMock(Mailer::class);

        /** \PHPUnit\Framework\MockObject\MockObject $mailer */
        $mailer
            ->expects($this->once())
            ->method('send')
            ->willReturnCallback(static function (MimeEmail $mimeEmail) use ($from, $to, $body): int {
                self::assertEquals([$from], $mimeEmail->getFrom());
                self::assertEquals([new Address($to->getValue())], $mimeEmail->getTo());
                self::assertEquals('Your confirmation of join', $mimeEmail->getSubject());
                self::assertEquals($body, $mimeEmail->getHtmlBody());

                return 1;
            })
        ;

        $sender = new JoinConfirmationSender(
            $mailer,
            $twig,
            $from->getAddress()
        );

        $sender->send($to, $token);
    }
}

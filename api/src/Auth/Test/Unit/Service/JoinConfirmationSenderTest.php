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

        $frontend = $this->createMock(FrontendUrlGenerator::class);

        $frontend
            ->expects($this->once())
            ->method('generate')
            ->with(
                $this->equalTo(JoinConfirmationSender::JOIN_URI),
                $this->equalTo(['token' => $token->getValue()])
            )
            ->willReturn($confirmUrl)
        ;

        $mailer = $this->createMock(Mailer::class);

        /** \PHPUnit\Framework\MockObject\MockObject $mailer */
        $mailer
            ->expects($this->once())
            ->method('send')
            ->willReturnCallback(static function (MimeEmail $mimeEmail) use ($from, $to, $confirmUrl): int {
                self::assertEquals([$from], $mimeEmail->getFrom());
                self::assertEquals([new Address($to->getValue())], $mimeEmail->getTo());
                self::assertEquals('Your confirm token', $mimeEmail->getSubject());
                self::assertStringContainsString($confirmUrl, $mimeEmail->getHtmlBody());

                return 1;
            })
        ;

        $sender = new JoinConfirmationSender(
            $mailer,
            $frontend,
            $from->getAddress()
        );

        $sender->send($to, $token);
    }
}

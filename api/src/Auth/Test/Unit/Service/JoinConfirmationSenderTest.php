<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Service;

use Ramsey\Uuid\Uuid;
use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use App\Auth\Service\JoinConfirmationSender;
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
        $confirmUri = '/join/confirm?token=' . $token->getValue();

        $mailer = $this->createMock(Mailer::class);

        /** \PHPUnit\Framework\MockObject\MockObject $mailer */
        $mailer
            ->expects($this->once())
            ->method('send')
            ->willReturnCallback(static function (MimeEmail $mimeEmail) use ($from, $to, $confirmUri): int {
                self::assertEquals([$from], $mimeEmail->getFrom());
                self::assertEquals([new Address($to->getValue())], $mimeEmail->getTo());
                self::assertEquals('Your confirm token', $mimeEmail->getSubject());
                self::assertStringContainsString($confirmUri, $mimeEmail->getHtmlBody());

                return 1;
            })
        ;


        $sender = new JoinConfirmationSender($mailer, $from->getAddress());

        $sender->send($to, $token);
    }
}

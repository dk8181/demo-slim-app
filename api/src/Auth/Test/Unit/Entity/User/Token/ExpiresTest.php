<?php

namespace App\Auth\Test\Unit\Entity\User\Token;

use Ramsey\Uuid\Uuid;
use App\Auth\Entity\User\Token;
use PHPUnit\Framework\TestCase;

/**
 * @covers Token::isExpiredTo
 */
class ExpiresTest extends TestCase
{
    public function testNotExpired(): void
    {
        $token = new Token(
            Uuid::uuid4()->toString(),
            $expires = new \DateTimeImmutable()
        );

        self::assertTrue($token->isExpiredTo($expires->modify('+45 secs')));
        self::assertTrue($token->isExpiredTo($expires));
        self::assertFalse($token->isExpiredTo($expires->modify('-45 secs')));
    }
}

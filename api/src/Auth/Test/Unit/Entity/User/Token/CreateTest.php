<?php

namespace App\Auth\Test\Unit\Entity\User\Token;

use Ramsey\Uuid\Uuid;
use App\Auth\Entity\User\Token;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers Token
 */
class CreateTest extends TestCase
{
    public function testSuccess(): void
    {
        $token = new Token(
            $value = Uuid::uuid4()->toString(),
            $expires = new DateTimeImmutable()
        );

        self::assertEquals($value, $token->getValue());
        self::assertEquals($expires, $token->getExpires());
    }

    public function testCase(): void
    {
        $value = Uuid::uuid4()->toString();

        $token = new Token(
            \mb_strtoupper($value),
            new DateTimeImmutable()
        );

        self::assertEquals($value, $token->getValue());
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Token('12335', new DateTimeImmutable());
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Token('', new DateTimeImmutable());
    }
}

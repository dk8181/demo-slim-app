<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User;

use App\Auth\Entity\User\Token;
use App\Auth\Test\Builder\UserBuilder;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * @covers App\Auth\Entity\User\User
 */
class ConfirmTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder)
            ->withJoinConfirmToken($token = $this->createToken())
            ->build();

        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());

        $user->confirmJoin(
            $token->getValue(),
            $token->getExpires()->modify('-1 day')
        );

        self::assertTrue($user->isActive());
        self::assertFalse($user->isWait());

        self::assertNull($user->getJoinConfirmToken());
    }

    public function testWrong(): void
    {
        $user = (new UserBuilder)
            ->withJoinConfirmToken($token = $this->createToken())
            ->build();

        $this->expectExceptionMessage('Token is not valid');

        $user->confirmJoin(
            Uuid::uuid4()->toString(),
            $token->getExpires()->modify('-1 day')
        );
    }

    public function testExpired(): void
    {
        $user = (new UserBuilder)
            ->withJoinConfirmToken($token = $this->createToken())
            ->build();

        $this->expectExceptionMessage('Token was expired.');

        $user->confirmJoin(
            $token->getValue(),
            $token->getExpires()->modify('+1 day')
        );
    }

    private function createToken(): Token
    {
        return new Token(
            Uuid::uuid4()->toString(),
            new \DateTimeImmutable('+1 day')
        );
    }
}

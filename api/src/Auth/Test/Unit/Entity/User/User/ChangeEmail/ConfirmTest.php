<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\ChangeEmail;

use Ramsey\Uuid\Uuid;
use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use PHPUnit\Framework\TestCase;
use App\Auth\Test\Builder\UserBuilder;

/**
 * @covers User::confirmEmailChanging
 */
class ConfirmTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder)
            ->active()
            ->build();

        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));
        $user->requestEmailChanging($token, $now, $new = new Email('new-john@info.org'));

        self::assertNotNull($user->getNewEmailToken());

        $user->confirmEmailChanging($token->getValue(), $now->modify('+2 hour'));

        self::assertNull($user->getNewEmail());
        self::assertNull($user->getNewEmailToken());
        self::assertEquals($new, $user->getEmail());
    }

    public function testInvalidToken(): void
    {
        $user = (new UserBuilder)
            ->active()
            ->build();

        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));

        $user->requestEmailChanging($token, $now, new Email('new-john@info.org'));

        $this->expectExceptionMessage('Token is not valid');
        $user->confirmEmailChanging('incorrect', $now->modify('+2 hour'));
    }

    public function testExpiredToken(): void
    {
        $user = (new UserBuilder)
            ->active()
            ->build();

        $now = new \DateTimeImmutable();
        $token = $this->createToken($now);

        $this->expectExceptionMessage('Token was expired.');
        $user->requestEmailChanging($token, $now, new Email('new-john@info.org'));
    }

    public function testNotRequested(): void
    {
        $user = (new UserBuilder)
            ->active()
            ->build();

        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));

        $this->expectExceptionMessage('Changing was not requested.');
        $user->confirmEmailChanging($token->getValue(), $now->modify('+2 hour'));
    }

    private function createToken(\DateTimeImmutable $expires): Token
    {
        return new Token(
            Uuid::uuid4()->toString(),
            $expires
        );
    }
}

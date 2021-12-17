<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User\ChangeEmail;

use Ramsey\Uuid\Uuid;
use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use PHPUnit\Framework\TestCase;
use App\Auth\Test\Builder\UserBuilder;

/**
 * @covers User::requestEmailChanging
 */
class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder)
            ->withEmail($old = new Email('old-john@info.org'))
            ->active()
            ->build();

        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));
        $user->requestEmailChanging($token, $now, $new = new Email('new-john@info.org'));

        self::assertEquals($token, $user->getNewEmailToken());
        self::assertEquals($old, $user->getEmail());
        self::assertEquals($new, $user->getNewEmail());
    }

    public function testSame(): void
    {
        $user = (new UserBuilder)
            ->withEmail($old = new Email('old-john@info.org'))
            ->active()
            ->build();

        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));

        $this->expectExceptionMessage('New email equals old email.');
        $user->requestEmailChanging($token, $now, $old);
    }

    public function testAlready(): void
    {
        $user = (new UserBuilder)
            ->active()
            ->build();

        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 day'));

        $user->requestEmailChanging($token, $now, $new = new Email('new-john@info.org'));

        $this->expectExceptionMessage('Email changing was already requested.');
        $user->requestEmailChanging($token, $now->modify('+1 hour'), $new);
    }

    public function testExpired(): void
    {
        $user = (new UserBuilder)
            ->active()
            ->build();

        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));

        $user->requestEmailChanging($token, $now, new Email('tmp-john@info.org'));

        $newDate = $now->modify('+2 hours');
        $newToken = $this->createToken($now->modify('+1 hour'));

        $this->expectExceptionMessage('Token was expired.');
        $user->requestEmailChanging($newToken, $newDate, $newEmail = new Email('new-john@info.org'));
    }

    public function testNotActive(): void
    {
        $user = (new UserBuilder)->build();
        $now = new \DateTimeImmutable();
        $token = $this->createToken($now->modify('+1 hour'));

        $this->expectExceptionMessage('User is not active.');
        $user->requestEmailChanging($token, $now, new Email('john-d@info.org'));
    }

    private function createToken(\DateTimeImmutable $expires): Token
    {
        return new Token(
            Uuid::uuid4()->toString(),
            $expires
        );
    }
}

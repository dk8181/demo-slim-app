<?php

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\Email;
use Ramsey\Uuid\Uuid;
use DateTimeImmutable;
use App\Auth\Entity\User\Id;
use InvalidArgumentException;
use App\Auth\Entity\User\User;
use App\Auth\Entity\User\Token;
use PHPUnit\Framework\TestCase;

/**
 * @covers User
 */
class RequestTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = new User(
            $id =  Id::generate(),
            $date = new DateTimeImmutable(),
            $email = new Email('john_dough@info.org'),
            $hash = 'hash',
            $token = new Token(Uuid::uuid4()->toString(), new DateTimeImmutable())
        );

        self::assertEquals($id, $user->getId());
        self::assertEquals($date, $user->getDate());
        self::assertEquals($email, $user->getEmail());
        self::assertEquals($hash, $user->getPasswordHash());
        self::assertEquals($token, $user->getJoinConfirmToken());
    }
}

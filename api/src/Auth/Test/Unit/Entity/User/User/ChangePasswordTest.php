<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User;

use App\Auth\Service\PasswordHasher;
use App\Auth\Test\Builder\UserBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers User::changePassword
 */
class ChangePasswordTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder)
            ->active()
            ->build();

        $hasher = $this->mockHasher(true, $hash = 'new-hash');

        $user->changePassword(
            'old-password',
            'new-password',
            $hasher
        );

        self::assertEquals($hash, $user->getPasswordHash());
    }

    public function testCurrentWorng(): void
    {
        $user = (new UserBuilder)
            ->active()
            ->build();

        $hasher = $this->mockHasher(false, 'new-hash');

        $this->expectExceptionMessage('Incorrect current password.');
        $user->changePassword(
            'wrong-old-password',
            'new-password',
            $hasher
        );
    }

    public function testViaNetwork(): void
    {
        $user = (new UserBuilder)
            ->viaNetwork()
            ->build();

        $hasher = $this->mockHasher(false, 'new-hash');

        $this->expectExceptionMessage('The user does not have an old password.');

        $user->changePassword(
            'old-password',
            'new-password',
            $hasher
        );
    }


    private function mockHasher(bool $isValid, string $hash): PasswordHasher
    {
        $hasher = $this->createStub(PasswordHasher::class);
        $hasher->method('validate')->willReturn($isValid);
        $hasher->method('hash')->willReturn($hash);

        return $hasher;
    }
}

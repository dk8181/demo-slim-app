<?php

declare(strict_types=1);

use App\Auth\Service\PasswordHasher;
use PHPUnit\Framework\TestCase;

/**
 * @covers PasswordHasher
 */
class PasswordHasherTest extends TestCase
{
    public function testHash(): void
    {
        $hasher = $this->getHasher();

        $hash = $hasher->hash($password = 'new-password');

        self::assertNotEmpty($hash);
        self::assertNotEquals($password, $hash);
    }

    public function testValidation(): void
    {
        $hash = $this->getHasher()->hash($password = 'new-pswd');

        self::assertTrue($this->getHasher()->validate($password, $hash));
        self::assertFalse($this->getHasher()->validate('some-pswd', $hash));
    }

    public function testEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->getHasher()->hash('');
    }

    private function getHasher(): PasswordHasher
    {
        return new PasswordHasher(16);
    }
}

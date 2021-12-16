<?php

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers Email
 */
class EmailTest extends TestCase
{
    public function testSuccess(): void
    {
        $email = new Email($value = 'john_dogh@info.org');

        self::assertEquals($value, $email->getValue());
    }

    public function testCase(): void
    {
        $email = new Email($value = 'John_Dogh@info.org');
        $expected = 'john_dogh@info.org';

        self::assertEquals($expected, $email->getValue());
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('not_email');
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('');
    }
}

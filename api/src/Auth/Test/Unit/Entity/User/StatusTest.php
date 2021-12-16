<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use PHPUnit\Framework\TestCase;
use App\Auth\Entity\User\Status;

class StatusTest extends TestCase
{
    public function testWait(): void
    {
        $wait = Status::wait();

        self::assertTrue($wait->isWait());
        self::assertFalse($wait->isActive());
    }

    public function testActive(): void
    {
        $active = Status::active();

        self::assertFalse($active->isWait());
        self::assertTrue($active->isActive());
    }
}

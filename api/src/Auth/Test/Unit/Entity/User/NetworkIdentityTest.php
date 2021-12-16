<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use PHPUnit\Framework\TestCase;
use App\Auth\Entity\User\NetworkIdentity;

/**
 * @covers NetworkIdentity
 */
class NetworkIdentityTest extends TestCase
{
    public function testSuccess(): void
    {
        $network = new NetworkIdentity($name = 'google', $identity = 'google-1');

        self::assertEquals($name, $network->getNetwork());
        self::assertEquals($identity, $network->getIdentity());
    }

    public function testEmptyName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $network = new NetworkIdentity('', 'google-1');
    }

    public function testEmptyIdentity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $network = new NetworkIdentity('google', '');
    }

    public function testEquals(): void
    {
        $network = new NetworkIdentity($name = 'google', $identity = 'google-1');

        self::assertTrue($network->isEqualTo(new NetworkIdentity($name, 'google-1')));
        self::assertFalse($network->isEqualTo(new NetworkIdentity($name, 'google-22')));
        self::assertFalse($network->isEqualTo(new NetworkIdentity('tweetter', 'tweetter-3')));
    }
}

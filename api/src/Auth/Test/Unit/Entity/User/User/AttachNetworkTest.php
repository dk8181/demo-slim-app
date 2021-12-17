<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User;

use App\Auth\Entity\User\NetworkIdentity;
use App\Auth\Test\Builder\UserBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers User::attachNetwork
 */
class AttachNetworkTest extends TestCase
{
    public function testSuccess(): void
    {
        $user = (new UserBuilder)
            ->active()
            ->build();

        $network = new NetworkIdentity('instagram', '10000001');
        $user->attachNetwork($network);

        self::assertCount(1, $networks = $user->getNetworks());
        self::assertEquals($network, $networks[0] ?? null);
    }

    public function testExists(): void
    {
        $user = (new UserBuilder)
            ->active()
            ->build();

        $network = new NetworkIdentity('instagram', '10000002');
        $user->attachNetwork($network);

        $this->expectExceptionMessage('This Network was already attached.');
        $user->attachNetwork($network);
    }
}

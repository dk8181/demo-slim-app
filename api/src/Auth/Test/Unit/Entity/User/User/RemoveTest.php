<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User\User;

use PHPUnit\Framework\TestCase;
use App\Auth\Test\Builder\UserBuilder;

/**
 * @covers User::remove
 */
class RemoveTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testSuccess(): void
    {
        $user = (new UserBuilder)
            ->build();

        $user->remove();
    }

    public function testActive(): void
    {
        $user = (new UserBuilder)
            ->active()
            ->build();

        $this->expectExceptionMessage('Unable to remove an active user.');

        $user->remove();
    }
}

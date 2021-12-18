<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\Role;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    public function testSuccess(): void
    {
        $role = new Role($name = Role::ADMIN);

        self::assertEquals($role->getName(), $name);
    }

    public function testIncorrect(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $role = new Role('su-manager');
    }

    public function testEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $role = new Role('');
    }

    public function testUserFactory(): void
    {
        $userRole = Role::user();

        self::assertEquals(Role::USER, $userRole->getName());
    }
}

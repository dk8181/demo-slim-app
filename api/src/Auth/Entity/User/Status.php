<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

class Status
{
    private const WAIT = 'wait';
    private const ACTIVE = 'active';

    private string $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function wait(): self
    {
        return new self(self::WAIT);
    }

    public static function active(): self
    {
        return new self(self::ACTIVE);
    }

    public function isActive(): bool
    {
        return self::ACTIVE === $this->name;
    }

    public function isWait(): bool
    {
        return self::WAIT === $this->name;
    }
}

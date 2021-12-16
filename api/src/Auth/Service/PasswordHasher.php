<?php

declare(strict_types=1);

namespace App\Auth\Service;

use Webmozart\Assert\Assert;

class PasswordHasher
{
    public function hash(string $password): string
    {
        Assert::notEmpty($password);

        /** @var string|false|null $hash */
        $hash = \password_hash($password, PASSWORD_ARGON2ID);

        if (null === $hash) {
            throw new \RuntimeException('Invalid hashing algorithm.');
        }

        if (false === $hash) {
            throw new \RuntimeException('Unable to generate hash.');
        }

        return $hash;
    }

    public function validate(string $password, string $hash): bool
    {
        return \password_verify($password, $hash);
    }
}

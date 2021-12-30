<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use DateTimeImmutable;
use Webmozart\Assert\Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Token
{
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private string $value;
    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private DateTimeImmutable $expires;

    public function __construct(string $value, DateTimeImmutable $expires)
    {
        Assert::uuid($value);
        $this->value   = \mb_strtolower($value);
        $this->expires = $expires;
    }

    public function validate(string $givenValue, \DateTimeImmutable $givenDate): void
    {
        if (! $this->isEqualTo($givenValue)) {
            throw new \DomainException('Token is not valid.');
        }

        if ($this->isExpiredTo($givenDate)) {
            throw new \DomainException('Token was expired.');
        }
    }

    private function isEqualTo(string $givenValue): bool
    {
        return $this->value === $givenValue;
    }

    public function isExpiredTo(\DateTimeImmutable $givenDate): bool
    {
        return $this->expires <= $givenDate;
    }

    public function isEmpty(): bool
    {
        return empty($this->value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getExpires(): DateTimeImmutable
    {
        return $this->expires;
    }
}

<?php

declare(strict_types=1);

namespace App\Auth\Service;

use DateInterval;
use Ramsey\Uuid\Uuid;
use DateTimeImmutable;
use App\Auth\Entity\User\Token;

class Tokenizer
{
    private DateInterval $interval;

    public function __construct(DateInterval $interval)
    {
        $this->interval = $interval;
    }

    public function generate(DateTimeImmutable $date): Token
    {
        return new Token(
            Uuid::uuid4()->toString(),
            $date->add($this->interval)
        );
    }
}

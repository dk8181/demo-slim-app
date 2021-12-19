<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Webmozart\Assert\Assert;

class Network
{
    private string $name;
    private string $identity;

    public function __construct(string $name, string $identity)
    {
        Assert::notEmpty($name);
        Assert::notEmpty($identity);

        $this->name = \mb_strtolower($name);
        $this->identity = \mb_strtolower($identity);
    }

    public function isEqualTo(self $givenNetwork): bool
    {
        return $this->getName() === $givenNetwork->getName()
            && $this->getIdentity() === $givenNetwork->getIdentity();
    }


    /**
     * Get the value of name
     *
     * @return  string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of identity
     *
     * @return  string
     */
    public function getIdentity(): string
    {
        return $this->identity;
    }
}

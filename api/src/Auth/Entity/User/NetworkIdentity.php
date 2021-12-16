<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Webmozart\Assert\Assert;

class NetworkIdentity
{
    private string $network;
    private string $identity;

    public function __construct(string $network, string $identity)
    {
        Assert::notEmpty($network);
        Assert::notEmpty($identity);

        $this->network = \mb_strtolower($network);
        $this->identity = \mb_strtolower($identity);
    }

    public function isEqualTo(self $givenNetwork): bool
    {
        return $this->getNetwork() === $givenNetwork->getNetwork()
            && $this->getIdentity() === $givenNetwork->getIdentity();
    }


    /**
     * Get the value of network
     *
     * @return  string
     */
    public function getNetwork(): string
    {
        return $this->network;
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

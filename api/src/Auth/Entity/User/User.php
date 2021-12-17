<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use App\Auth\Service\PasswordHasher;

class User
{
    private Id $id;
    private \DateTimeImmutable $date;
    private Email $email;
    private ?string $passwordHash = null;
    private Status $status;
    private ?Token $joinConfirmToken = null;
    private \ArrayObject $networks;
    private ?Token $passwordResetToken = null;

    private function __construct(
        Id $id,
        \DateTimeImmutable $date,
        Email $email,
        Status $status
    ) {
        $this->id = $id;
        $this->date = $date;
        $this->email = $email;
        $this->status = $status;
        $this->networks = new \ArrayObject();
    }

    public static function requestJoinByEmail(
        Id $id,
        \DateTimeImmutable $date,
        Email $email,
        string $passwordHash,
        Token $token
    ): self {
        $user = new self($id, $date, $email, Status::wait());
        $user->passwordHash = $passwordHash;
        $user->joinConfirmToken = $token;

        return $user;
    }

    public static function joinByNetwork(
        Id $id,
        \DateTimeImmutable $date,
        Email $email,
        NetworkIdentity $identity
    ): self {
        $user = new self($id, $date, $email, Status::active());
        $user->networks->append($identity);

        return $user;
    }

    public function confirmJoin(string $token, \DateTimeImmutable $date): void
    {
        if (null === $this->joinConfirmToken) {
            throw new \DomainException('Confirmation not possable');
        }

        $this->joinConfirmToken->validate($token, $date);
        $this->status = Status::active();
        $this->joinConfirmToken = null;
    }

    public function attachNetwork(NetworkIdentity $identity): void
    {
        /** @var NetworkIdentity $existing */
        foreach ($this->networks as $existing) {
            if ($existing->isEqualTo($identity)) {
                throw new \DomainException('This Network was already attached.');
            }
        }

        $this->networks->append($identity);
    }

    public function requestPasswordReset(Token $token, \DateTimeImmutable $date): void
    {
        if (! $this->isActive()) {
            throw new \DomainException('User is not active.');
        }

        if (
            $this->passwordResetToken !== null
            && ! $this->passwordResetToken->isExpiredTo($date)
        ) {
            throw new \DomainException('Passord resetting was already requested.');
        }

        $this->passwordResetToken = $token;
    }

    public function changePassword(
        string $current,
        string $new,
        PasswordHasher $hasher
    ): void {
        if (null === $this->passwordHash) {
            throw new \DomainException('The user does not have an old password.');
        }

        if (! $hasher->validate($current, $this->passwordHash)) {
            throw new \DomainException('Incorrect current password.');
        }

        $this->passwordHash = $hasher->hash($new);
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function getPasswordResetToken(): ?Token
    {
        return $this->passwordResetToken;
    }

    public function getJoinConfirmToken(): ?Token
    {
        return $this->joinConfirmToken;
    }

    public function isWait(): bool
    {
        return $this->status->isWait();
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    /**
     *
     * @return NetworkIdentity[]
     */
    public function getNetworks(): array
    {
        /** @var NetworkIdentity[] */
        return $this->networks->getArrayCopy();
    }
}

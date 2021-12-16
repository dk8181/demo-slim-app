<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

class User
{
    private Id $id;
    private \DateTimeImmutable $date;
    private Email $email;
    private string $passwordHash;
    private ?Token $joinConfirmToken;
    private Status $status;

    public function __construct(
        Id $id,
        \DateTimeImmutable $date,
        Email $email,
        string $passwordHash,
        Token $token
    ) {
        $this->id = $id;
        $this->date = $date;
        $this->email = $email;
        $this->status = Status::wait();
        $this->passwordHash = $passwordHash;
        $this->joinConfirmToken = $token;
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
}

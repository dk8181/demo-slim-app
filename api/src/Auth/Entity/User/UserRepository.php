<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

interface UserRepository
{
    public function hasByEmail(Email $email): bool;
    public function hasByNetwork(Network $network): bool;

    public function findByJoinConfirmToken(string $tokenValue): ?User;
    public function findByNewEmailToken(string $tokenValue): ?User;
    public function findByPasswordResetToken(string $tokenValue): ?User;

    public function add(User $user): void;
    public function remove(User $user): void;

    /**
     * @param Id $userId
     * @return User
     * @throws \DomainException
     */
    public function get(Id $userId): User;

    /**
     * @param Email $email
     * @return User
     * @throws \DomainException
     */
    public function getByEmail(Email $email): User;
}

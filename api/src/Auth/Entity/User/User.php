<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use App\Auth\Entity\User\Role;
use App\Auth\Service\PasswordHasher;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="auth_users")
 */
class User
{
    /**
     * @ORM\Column(type="auth_user_id")
     * @ORM\Id
     */
    private Id $id;
    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $date;
    /**
     * @ORM\Column(type="auth_user_email", unique=true)
     */
    private Email $email;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $passwordHash = null;
    /**
     * @ORM\Column(type="auth_user_status", length=16)
     */
    private Status $status;
    /**
     * @ORM\Embedded(class="Token")
     */
    private ?Token $joinConfirmToken = null;
    /**
     * @ORM\Embedded(class="Token")
     */
    private ?Token $passwordResetToken = null;
    /**
     * @ORM\Column(type="auth_user_email", nullable=true)
     */
    private ?Email $newEmail = null;
    /**
     * @ORM\Embedded(class="Token")
     */
    private ?Token $newEmailToken = null;
    /**
     * @ORM\Column(type="auth_user_role", length=16)
     */
    private Role $role;
    private Collection $networks;

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
        $this->networks = new ArrayCollection();
        $this->role = Role::user();
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
        Network $network
    ): self {
        $user = new self($id, $date, $email, Status::active());
        $user->networks->add($network);

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

    public function attachNetwork(Network $network): void
    {
        /** @var Network $existing */
        foreach ($this->networks as $existing) {
            if ($existing->isEqualTo($network)) {
                throw new \DomainException('This Network was already attached.');
            }
        }

        $this->networks->add($network);
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

    public function resetPassword(
        string $tokenName,
        \DateTimeImmutable $date,
        string $hash
    ): void {
        if ($this->passwordResetToken === null) {
            throw new \DomainException('Resetting is not requested.');
        }
        $this->passwordResetToken->validate($tokenName, $date);
        $this->passwordResetToken = null;
        $this->passwordHash = $hash;
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

    public function requestEmailChanging(
        Token $token,
        \DateTimeImmutable $date,
        Email $newEmail
    ): void {
        if ($token->isExpiredTo($date)) {
            throw new \DomainException('Token was expired.');
        }

        if (! $this->isActive()) {
            throw new \DomainException('User is not active.');
        }

        if ($this->email->isEqualTo($newEmail)) {
            throw new \DomainException('New email equals old email.');
        }

        if ($this->newEmailToken !== null && ! $this->newEmailToken->isExpiredTo($date)) {
            throw new \DomainException('Email changing was already requested.');
        }

        $this->newEmail = $newEmail;
        $this->newEmailToken = $token;
    }

    public function confirmEmailChanging(
        string $tokenValue,
        \DateTimeImmutable $date
    ): void {
        if ($this->newEmail === null || $this->newEmailToken === null) {
            throw new \DomainException('Changing was not requested.');
        }

        $this->newEmailToken->validate($tokenValue, $date);
        $this->email = $this->newEmail;
        $this->newEmail = null;
        $this->newEmailToken = null;
    }

    public function changeRole(Role $newRole): void
    {
        if (! $this->role->equalTo($newRole)) {
            $this->role = $newRole;
        }
    }

    public function remove(): void
    {
        if (! $this->isWait()) {
            throw new \DomainException('Unable to remove an active user.');
        }
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

    public function getRole(): Role
    {
        return $this->role;
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
     * @return Network[]
     */
    public function getNetworks(): array
    {
        /** @var Network[] */
        return $this->networks->toArray();
    }

    public function getNewEmail(): ?Email
    {
        return $this->newEmail;
    }

    public function getNewEmailToken(): ?Token
    {
        return $this->newEmailToken;
    }

    /**
     * @ORM\PostLoad()
     */
    public function checkEmbeds(): void
    {
        if ($this->joinConfirmToken && $this->joinConfirmToken->isEmpty()) {
            $this->joinConfirmToken = null;
        }

        if ($this->passwordResetToken && $this->passwordResetToken->isEmpty()) {
            $this->passwordResetToken = null;
        }

        if ($this->newEmailToken && $this->newEmailToken->isEmpty()) {
            $this->newEmailToken = null;
        }

    }
}

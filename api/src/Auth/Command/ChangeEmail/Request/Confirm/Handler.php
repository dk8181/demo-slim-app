<?php

declare(strict_types=1);

namespace App\Auth\Command\ChangeEmail\Request;

use App\Flusher;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\UserRepository;

class Handler
{
    private UserRepository $users;
    private Flusher $flusher;

    public function __construct(
        UserRepository $users,
        Flusher $flusher,
    ) {
        $this->users = $users;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->findByNewEmailToken($command->token);

        if (! $user) {
            throw new \DomainException('Given token is not valid.');
        }

        $user->confirmEmailChanging(
            $command->token,
            new \DateTimeImmutable()
        );

        $this->flusher->flush();
    }
}

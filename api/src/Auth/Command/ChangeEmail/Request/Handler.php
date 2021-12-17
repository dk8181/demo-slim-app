<?php

declare(strict_types=1);

namespace App\Auth\Command\ChangeEmail\Request;

use App\Flusher;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Email;
use App\Auth\Service\Tokenizer;
use App\Auth\Entity\User\UserRepository;
use App\Auth\Service\NewEmailConfirmTokenSender;

class Handler
{
    private UserRepository $users;
    private Tokenizer $tokenizer;
    private Flusher $flusher;
    private NewEmailConfirmTokenSender $sender;

    public function __construct(
        UserRepository $users,
        Tokenizer $tokenizer,
        Flusher $flusher,
        NewEmailConfirmTokenSender $sender
    ) {
        $this->users = $users;
        $this->tokenizer = $tokenizer;
        $this->flusher = $flusher;
        $this->sender = $sender;
    }

    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->id));

        $email = new Email($command->email);

        if ($this->users->hasByEmail($email)) {
            throw new \DomainException('Given email already used.');
        }

        $date = new \DateTimeImmutable();

        $user->requestEmailChanging(
            $token = $this->tokenizer->generate($date),
            $date,
            $email
        );

        $this->flusher->flush();

        $this->sender->send($email, $token);
    }
}

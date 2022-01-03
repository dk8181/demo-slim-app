<?php

declare(strict_types=1);

namespace App\Auth\Fixture;

use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\User;
use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use Ramsey\Uuid\Nonstandard\Uuid;
use App\Auth\Service\PasswordHasher;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

class UserFixture extends AbstractFixture
{
    private PasswordHasher $hasher;

    public function __construct()
    {
        $hasher = new PasswordHasher();
    }

    public function load(ObjectManager $manager): void
    {
        $user = User::requestJoinByEmail(
            new Id('00000000-0000-0000-0000-000000000001'),
            $date = new \DateTimeImmutable('-30 days'),
            new Email('john_crishum@info.org'),
            $this->hasher->hash('secret'),
            new Token($value = Uuid::uuid4()->toString(), $date->modify('+1 day'))
        );

        $user->confirmJoin($value, $date->modify('+1 day'));

        $manager->persist($user);

        $manager->flush();
    }
}

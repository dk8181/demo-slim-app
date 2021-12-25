<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository
{
    private EntityManagerInterface $em;
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $em, EntityRepository $repository)
    {
        $this->em = $em;
        $this->repository = $repository;
    }

    /**
     * @param Id $userId
     * @return User
     * @throws \DomainException
     */
    public function get(Id $userId): User
    {
        if (! $user = $this->repository->find($userId->getValue())) {
            throw new \DomainException('User was not found.');
        }

        /** @var User $user */
        return $user;
    }

    /**
     * @param Email $email
     * @return User
     * @throws \DomainException
     */
    public function getByEmail(Email $email): User
    {
        if (! $user = $this->repository->findOneBy(
            [
                'email' => $email->getValue(),
            ]
        )) {
            throw new \DomainException('User was not found.');
        }

        /** @var User $user */
        return $user;
    }

    public function findByJoinConfirmToken(string $tokenValue): ?User
    {
        /** @psalm-return User|null */
        return $this
            ->repository
            ->findOneBy(
                [
                    'joinConfirmToken.value' => $tokenValue,
                ]
            );
    }

    public function findByNewEmailToken(string $tokenValue): ?User
    {
        /** @psalm-return User|null */
        return $this
            ->repository
            ->findOneBy(
                [
                    'newEmailToken.value' => $tokenValue,
                ]
            );
    }

    public function findByPasswordResetToken(string $tokenValue): ?User
    {
        /** @psalm-return User|null */
        return $this
            ->repository
            ->findOneBy(
                [
                    'passwordResetToken.value' => $tokenValue,
                ]
            );
    }

    public function hasByEmail(Email $email): bool
    {
        return $this
            ->repository
            ->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.email = :email')
            ->setParameter(':email', $email->getValue())
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }


    public function hasByNetwork(Network $network): bool
    {
        return $this
            ->repository
            ->createQueryBuilder('t')
            ->innerJoin('t.networks', 'n')
            ->andWhere('n.network.name = :name and n.network.identity = :identity')
            ->setParameter(':name', $network->getName())
            ->setParameter(':identity', $network->getIdentity())
            ->getQuery()
            ->getSingleScalarResult() > 0;

    }

    public function add(User $user): void
    {
        $this->em->persist($user);
    }

    public function remove(User $user): void
    {
        $this->em->remove($user);
    }
}

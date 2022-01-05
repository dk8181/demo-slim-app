<?php

declare(strict_types=1);

use App\Auth\Entity\User\User;
use Doctrine\ORM\EntityRepository;
use Psr\Container\ContainerInterface;
use App\Frontend\FrontendUrlGenerator;
use App\Auth\Entity\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Auth\Service\JoinConfirmationSender;
use Symfony\Component\Mailer\MailerInterface;

return [
    UserRepository::class => static function (ContainerInterface $container): UserRepository {
        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);

        /** @var EntityRepository $repository */
        $repository = $em->getRepository(User::class);

        return new UserRepository($em, $repository);
    },

    JoinConfirmationSender::class => static function (ContainerInterface $container): JoinConfirmationSender {
        /** @var MailerInterface $mailer */
        $mailer = $container->get(MailerInterface::class);

        /** @var FrontendUrlGenerator $frontendUrlGenerator */
        $frontendUrlGenerator = $container->get(FrontendUrlGenerator::class);

        /**
         * @psalm-suppress MixedArrayAccess
         * @psalm-var array{from:string} $mailerConfig
         */
        $mailerConfig = $container->get('config')['mailer'];

        return new JoinConfirmationSender(
            $mailer,
            $frontendUrlGenerator,
            $mailerConfig['from']
        );
    },
];

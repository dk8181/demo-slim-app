<?php

declare(strict_types=1);

use Twig\Environment;
use App\Auth\Entity\User\User;
use App\Auth\Service\Tokenizer;
use Doctrine\ORM\EntityRepository;
use Psr\Container\ContainerInterface;
use App\Auth\Entity\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Auth\Service\JoinConfirmationSender;
use Symfony\Component\Mailer\MailerInterface;
use App\Auth\Service\PasswordResetTokenSender;
use App\Auth\Service\NewEmailConfirmTokenSender;

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

        /** @var Environment $twig */
        $twig = $container->get(Environment::class);

        /**
         * @psalm-suppress MixedArrayAccess
         * @psalm-var array{from:string} $mailerConfig
         */
        $mailerConfig = $container->get('config')['mailer'];

        return new JoinConfirmationSender(
            $mailer,
            $twig,
            $mailerConfig['from']
        );
    },

    NewEmailConfirmTokenSender::class => static function (ContainerInterface $container): NewEmailConfirmTokenSender {
        /** @var MailerInterface $mailer */
        $mailer = $container->get(MailerInterface::class);

        /** @var Environment $twig */
        $twig = $container->get(Environment::class);

        /**
         * @psalm-suppress MixedArrayAccess
         * @psalm-var array{from:string} $mailerConfig
         */
        $mailerConfig = $container->get('config')['mailer'];

        return new NewEmailConfirmTokenSender(
            $mailer,
            $twig,
            $mailerConfig['from']
        );
    },

    PasswordResetTokenSender::class => static function (ContainerInterface $container): PasswordResetTokenSender {
        /** @var MailerInterface $mailer */
        $mailer = $container->get(MailerInterface::class);

        /** @var Environment $twig */
        $twig = $container->get(Environment::class);

        /**
         * @psalm-suppress MixedArrayAccess
         * @psalm-var array{from:string} $mailerConfig
         */
        $mailerConfig = $container->get('config')['mailer'];

        return new PasswordResetTokenSender(
            $mailer,
            $twig,
            $mailerConfig['from']
        );
    },

    Tokenizer::class => static function (ContainerInterface $container) : Tokenizer {
        /**
         * @psalm-suppress MixedArrayAccess
         * @psalm-var array{token_ttl:string} $config
         */
        $config = $container->get('config')['auth'];

        return new Tokenizer(new \DateInterval($config['token_ttl']));
    },

    'config' => [
        'auth' => [
            'token_ttl' => 'PT1H',
        ],
    ],
];

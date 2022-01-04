<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;

return [
    MailerInterface::class => static function (ContainerInterface $container) {
        /**
         * @psalm-suppress MixedArrayAccess
         * @psalm-var array{
         *  host:string,
         *  port:int,
         *  user:string,
         *  password:string,
         *  encryption:string
         * } $config
         */
        $config = $container->get('config')['mailer'];

        $dsn = 'smtp://'
            . $config['user']
            . ':'
            . $config['password']
            . '@'
            . $config['host']
            . ':'
            . $config['port']
            . '?'
            . 'verify_peer='
            . $config['verify_peer']
        ;

        $transport = Transport::fromDsn($dsn);

        return new Mailer($transport);
    },

    'config' => [
        'mailer' => [
            'user'     => getenv('MAILER_USER'),
            'password' => getenv('MAILER_PASSWORD'),
            'host'     => getenv('MAILER_HOST'),
            'port'     => getenv('MAILER_PORT'),
            'encryption' => getenv('MAILER_ENCRYPTION'),
            'from'       => getenv('MAILER_FROM_EMAIL'),
            'verify_peer' => 1,
        ],
    ],
];

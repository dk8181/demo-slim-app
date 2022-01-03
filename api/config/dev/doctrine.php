<?php

declare(strict_types=1);

use App\Data\Doctrine\FixDefaultSchemaSubscriber;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

return [
    \Doctrine\Common\DataFixtures\Loader::class => static function (ContainerInterface $container) {
        return new Loader();
    },

    ORMPurger::class => static function (ContainerInterface $container) {
        return new ORMPurger($container->get(EntityManagerInterface::class));
    },

    ORMExecutor::class => static function (ContainerInterface $container) {
        $em = $container->get(EntityManagerInterface::class);

        return new ORMExecutor($em, $container->get(ORMPurger::class));
    },

    'config' => [
        'doctrine' => [
            'dev_mode' => true,
            'proxy_dir' => __DIR__ . '/../../var/cache/' . PHP_SAPI . '/doctrine/proxy',
            'subscribers' => [
                FixDefaultSchemaSubscriber::class,
            ],
        ],
    ],
];

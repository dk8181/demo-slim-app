<?php

declare(strict_types=1);

use App\Flusher;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;

return [
    Flusher::class => function (ContainerInterface $container): Flusher {
        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);

        return new Flusher($em);
    }
];

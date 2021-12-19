#!/usr/bin/env php
<?php

declare(strict_types=1);

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

require __DIR__ . '/../vendor/autoload.php';

/** @var Psr\Container\ContainerInterface $container */
$container = require __DIR__ . '/../config/container.php';

$cli = new Application('Console', '1.0.0 (stable)');

/**
 * @var string[] $commands
 * @psalm-suppress MixedArrayAccess
 */
$commands = $container->get('config')['console']['commands'];

$entityManager = $container->get(EntityManagerInterface::class);
$cli->getHelperSet()->set(new EntityManagerHelper($entityManager), 'em');

foreach ($commands as $name) {
    /** @var Command $command */
    $command = $container->get($name);
    $cli->add($command);
}

$cli->run();

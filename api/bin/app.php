#!/usr/bin/env php
<?php

declare(strict_types = 1);

use Symfony\Component\Console\Application;

require __DIR__ . '/../vendor/autoload.php';

/** @var Psr\Container\ContainerInterface $container */
$container = require __DIR__ . '/../config/container.php';

$cli = new Application('Console', '1.0.0 (stable)');

$commands = $container->get('config')['console']['commands'];

foreach ($commands as $command) {
    $cli->add($container->get($command));
}

$cli->run();

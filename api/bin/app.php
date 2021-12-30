#!/usr/bin/env php
<?php

declare(strict_types=1);

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\ExistingConfiguration;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;

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
$connection = $entityManager->getConnection();

$configuration = new Configuration($connection);
$configuration->addMigrationsDirectory('App\Data\Migration', __DIR__ . '/../src/Data/Migration');
$configuration->setAllOrNothing(true);
$configuration->setCheckDatabasePlatform(true);

$storageConfiguration = new TableMetadataStorageConfiguration();
$storageConfiguration->setTableName('migrations');
$configuration->setMetadataStorageConfiguration($storageConfiguration);

$dependencyFactory = DependencyFactory::fromEntityManager(
    new ExistingConfiguration($configuration),
    new ExistingEntityManager($entityManager)
);

$cli->setCatchExceptions(true);
$cli->getHelperSet()->set(new EntityManagerHelper($entityManager), 'em');

\Doctrine\Migrations\Tools\Console\ConsoleRunner::addCommands(
    $cli,
    $dependencyFactory
);

foreach ($commands as $name) {
    /** @var Command $command */
    $command = $container->get($name);
    $cli->add($command);
}

$cli->run();

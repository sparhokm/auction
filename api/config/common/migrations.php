<?php

declare(strict_types=1);

use Doctrine\Migrations;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Tools\Console\Command;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

return [
    DependencyFactory::class => static function (ContainerInterface $container) {
        $entityManager = $container->get(EntityManagerInterface::class);

        $configuration = new Doctrine\Migrations\Configuration\Configuration();
        $configuration->addMigrationsDirectory('App\Data\Migration', __DIR__ . '/../../src/Data/Migration');
        $configuration->setAllOrNothing(true);
        $configuration->setCheckDatabasePlatform(false);

        $storageConfiguration = new Migrations\Metadata\Storage\TableMetadataStorageConfiguration();
        $storageConfiguration->setTableName('migrations');

        $configuration->setMetadataStorageConfiguration($storageConfiguration);

        return DependencyFactory::fromEntityManager(
            new Migrations\Configuration\Migration\ExistingConfiguration($configuration),
            new Migrations\Configuration\EntityManager\ExistingEntityManager($entityManager)
        );
    },
    Command\ExecuteCommand::class => static function (ContainerInterface $container) {
        return new Command\ExecuteCommand($container->get(DependencyFactory::class));
    },
    Command\MigrateCommand::class => static function (ContainerInterface $container) {
        return new Command\MigrateCommand($container->get(DependencyFactory::class));
    },
    Command\LatestCommand::class => static function (ContainerInterface $container) {
        return new Command\LatestCommand($container->get(DependencyFactory::class));
    },
    Command\ListCommand::class => static function (ContainerInterface $container) {
        return new Command\ListCommand($container->get(DependencyFactory::class));
    },
    Command\StatusCommand::class => static function (ContainerInterface $container) {
        return new Command\StatusCommand($container->get(DependencyFactory::class));
    },
    Command\UpToDateCommand::class => static function (ContainerInterface $container) {
        return new Command\UpToDateCommand($container->get(DependencyFactory::class));
    },
    Command\DiffCommand::class => static function (ContainerInterface $container) {
        return new Command\DiffCommand($container->get(DependencyFactory::class));
    },
    Command\GenerateCommand::class => static function (ContainerInterface $container) {
        return new Command\GenerateCommand($container->get(DependencyFactory::class));
    },
];

#!/usr/bin/env php
<?php

declare(strict_types=1);

use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\ExistingConfiguration;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
use Doctrine\Migrations\Tools\Console\Helper\ConfigurationHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

require __DIR__ . '/../vendor/autoload.php';

if (getenv('SENTRY_DNS')) {
    Sentry\Init(['dns' => getenv('SENTRY_DNS')]);
}

/** @var ContainerInterface $container */
$container = require __DIR__ . '/../config/container.php';

$cli = new Application('Console');

if (getenv('SENTRY_DNS')) {
    $cli->setCatchExceptions(false);
}

/**
 * @var string[] $commands
 * @psalm-suppress MixedArrayAccess
 */
$commands = $container->get('config')['console']['commands'];

$cli->getHelperSet()->set(new EntityManagerHelper($container->get(EntityManagerInterface::class)), 'em');

foreach ($commands as $name) {
    /** @var Command $command */
    $command = $container->get($name);
    $cli->add($command);
}

$cli->run();

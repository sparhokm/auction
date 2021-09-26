<?php

declare(strict_types=1);

use Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand;
use Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand;

return [
    'config' => [
        'console' => [
            'commands' => [
                CreateCommand::class,
                DropCommand::class
            ]
        ]
    ]
];

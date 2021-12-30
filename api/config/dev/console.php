<?php

declare(strict_types=1);

use Doctrine\ORM\Tools\Console\Command\SchemaTool;
use Doctrine\Migrations\Tools\Console\Command;

return [
    'config' => [
        'console' => [
            'commands' => [
                SchemaTool\DropCommand::class,
            ],
        ],
    ],
];

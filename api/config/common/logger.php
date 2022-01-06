<?php

declare(strict_types=1);

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Monolog\Handler\StreamHandler;
use Psr\Container\ContainerInterface;

return [
    LoggerInterface::class => static function (ContainerInterface $container): Logger {
        /**
         * @psalm-suppress MixedArrayAccess
         * @psalm-var array{
         *   debug:bool,
         *   file:string,
         *   stderr:bool,
         * } $config
         */
        $config = $container->get('config')['logger'];

        $level = $config['debug'] ? Logger::DEBUG : Logger::INFO;

        $mLogger = new Logger('API');

        if ($config['stderr']) {
            $mLogger->pushHandler(new StreamHandler('php://stderr', $level));
        }

        if (! empty($config['file'])) {
            $mLogger->pushHandler(new StreamHandler($config['file'], $level));
        }

        return $mLogger;
    },

    'config' => [
        'logger' => [
            'debug' => (bool) getenv('APP_DEBUG'),
            'file' => null,
            'stderr' => true,
        ],
    ],
];

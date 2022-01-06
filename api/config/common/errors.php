<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Middleware\ErrorMiddleware;

return [
    ErrorMiddleware::class => static function(ContainerInterface $container): ErrorMiddleware {
        /** @var CallableResolverInterface $callableResolver */
        $callableResolver = $container->get(CallableResolverInterface::class);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        /**
         * @phan-suppress MixedArrayAccess
         * @psalm-var array{
         *  display_error_details:bool,
         *  log_errors:bool,
         *  log_error_details:bool
         * } $config
         */
        $config = $container->get('config')['errors'];

        return new ErrorMiddleware(
            $callableResolver,
            $responseFactory,
            $config['display_error_details'],
            $config['log_errors'],
            $config['log_error_details']
        );
    },

    'config' => [
        'errors' => [
            'display_error_details' => (bool) getenv('APP_DEBUG'),
            'log_errors' => true,
            'log_error_details' => true,
        ],
    ],
];

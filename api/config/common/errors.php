<?php

declare(strict_types=1);

use Psr\Log\LoggerInterface;
use Slim\Middleware\ErrorMiddleware;
use Psr\Container\ContainerInterface;
use App\ErrorHandler\LoggedErrorHandler;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Interfaces\CallableResolverInterface;
use App\ErrorHandler\SentryErrorHandlerDecorator;

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
         * } $config
         */
        $config = $container->get('config')['errors'];

        $middleware =  new ErrorMiddleware(
            $callableResolver,
            $responseFactory,
            $config['display_error_details'],
            true,
            true
        );

        /** @var LoggerInterface logger */
        $logger = $container->get(LoggerInterface::class);

        $loggedErrorHandler = new LoggedErrorHandler(
            $callableResolver,
            $responseFactory,
            $logger
        );

        if ($config['use_sentry']) {
            $middleware->setDefaultErrorHandler(
                new SentryErrorHandlerDecorator($loggedErrorHandler)
            );
        }

        if (! $config['use_sentry']) {
            $middleware->setDefaultErrorHandler($loggedErrorHandler);
        }

        return $middleware;
    },

    'config' => [
        'errors' => [
            'display_error_details' => (bool) \getenv('APP_DEBUG'),
            'use_sentry' => (bool) \getenv('SENTRY_DSN'),
        ],
    ],
];

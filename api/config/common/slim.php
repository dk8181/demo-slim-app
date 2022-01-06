<?php

declare(strict_types=1);

use Slim\CallableResolver;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\CallableResolverInterface;

return [
    CallableResolverInterface::class => static function (ContainerInterface $container): CallableResolver {
        return new CallableResolver($container);
    },
];

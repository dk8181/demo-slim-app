<?php

declare(strict_types=1);

use Laminas\ConfigAggregator\PhpFileProvider;
use Laminas\ConfigAggregator\ConfigAggregator;

$aggrigator = new ConfigAggregator([
    new PhpFileProvider(__DIR__ . '/common/*.php'),
    new PhpFileProvider(__DIR__ . '/' . (getenv('APP_ENV') ?: 'prod') . '/*.php'),
]);

return $aggrigator->getMergedConfig();

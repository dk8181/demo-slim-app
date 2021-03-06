<?php

declare(strict_types=1);

namespace App\ErrorHandler;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\Handlers\ErrorHandler;
use Slim\Interfaces\CallableResolverInterface;

/**
 * @phan-suppress PropertyNotSetInConstructor
 */
class LoggedErrorHandler extends ErrorHandler
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        CallableResolverInterface $callableResolver,
        ResponseFactoryInterface $responseFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($callableResolver, $responseFactory);
        $this->logger = $logger;
    }

    /**
     * Ovewrite parent method.
     * Write to the error log with \Monolog\Logger
     *
     * @return void
     */
    protected function writeToErrorLog(): void
    {
        $this
            ->logger
            ->error(
                $this->exception->getMessage(),
                [
                    'exception' => $this->exception,
                    'url' => (string) $this->request->getUri(),
                ]
            );
    }
}

<?php

declare(strict_types=1);

namespace App\ErrorHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\ErrorHandlerInterface;

class SentryErrorHandlerDecorator implements ErrorHandlerInterface
{
    private ErrorHandlerInterface $next;

    public function __construct(ErrorHandlerInterface $next)
    {
        $this->next = $next;
    }

    public function __invoke(
       ServerRequestInterface $request,
       \Throwable $exception,
       bool $displayErrorDetails,
       bool $logErrors,
       bool $logErrorDetails
    ): ResponseInterface {

        \Sentry\captureException($exception);

        return ($this->next)(
            $request,
            $exception,
            $displayErrorDetails,
            $logErrors,
            $logErrorDetails
        );
    }
}

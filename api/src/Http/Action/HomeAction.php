<?php

declare(strict_types = 1);

namespace App\Http\Action;

use App\Http;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class HomeAction
{
    public function __invoke(Request $request, Response $response, $args): Response
    {
        $content = ['title' => 'Hi!'];

        return Http::json($response, $content);
    }
}

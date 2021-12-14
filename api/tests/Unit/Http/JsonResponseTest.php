<?php

declare(strict_types=1);

namespace Test\Unit\Http;

use App\Http\JsonResponse;
use PHPUnit\Framework\TestCase;

class JsonResponseTest extends TestCase
{
    public function testWithCode(): void
    {
        $response = new JsonResponse(0, 201);

        self::assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        self::assertEquals('0', $response->getBody()->getContents());
        self::assertEquals(201, $response->getStatusCode());
    }

    public function testNull(): void
    {
        $response = new JsonResponse(null);

        self::assertEquals('null', $response->getBody()->getContents());
        self::assertEquals(200, $response->getStatusCode());
    }

    public function testString(): void
    {
        $response = new JsonResponse('13');

        self::assertEquals('"13"', $response->getBody()->getContents());
        self::assertEquals(200, $response->getStatusCode());
    }

    public function testObject(): void
    {
        $object = new \stdClass();
        $object->str = 'value';
        $object->int = 13;
        $object->none = null;

        $response = new JsonResponse($object);

        self::assertEquals('{"str":"value","int":13,"none":null}', $response->getBody()->getContents());
        self::assertEquals(200, $response->getStatusCode());
    }

    public function testArray(): void
    {
        $arrayContent = [
            'str' => 'value',
            'int' => 13,
            'none' => null,
        ];


        $response = new JsonResponse($arrayContent);

        self::assertEquals('{"str":"value","int":13,"none":null}', $response->getBody()->getContents());
        self::assertEquals(200, $response->getStatusCode());
    }
}

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

    /**
     * @dataProvider getCases
     *
     * @param mixed $source
     * @param mixed $expect
     * @return void
     */
    public function testResponse($source, $expect): void
    {
        $response = new JsonResponse($source);

        self::assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        self::assertEquals($expect, $response->getBody()->getContents());
        self::assertEquals(200, $response->getStatusCode());
    }

    /**
     * @return array<mixed>
     */
    public function getCases(): array
    {
        $object = new \stdClass();
        $object->str = 'value';
        $object->int = 13;
        $object->none = null;


        $array = [
            'str' => 'value',
            'int' => 13,
            'none' => null,
        ];

        return [
            'null' => [null, 'null'],
            'empty' => ['', '""'],
            'number' => [13, '13'],
            'string' => ['13', '"13"'],
            'object' => [$object, '{"str":"value","int":13,"none":null}'],
            'array' => [$array, '{"str":"value","int":13,"none":null}'],
        ];
    }
}

<?php

declare(strict_types=1);

namespace Test\Functional;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Test\Functional\Helper\JsonHelper;

/**
 * @coversNothing
 */
class NotFoundTest extends WebTestCase
{
    use ArraySubsetAsserts;

    public function testNotFound(): void
    {
        $response = $this->app()->handle(self::json('GET', '/not-found'));

        self::assertEquals(404, $response->getStatusCode());
        self::assertJson($body = (string) $response->getBody());

        // $bodyDecoded = \json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        $bodyDecoded = JsonHelper::decode($body);

        self::assertArraySubset([
            'message' => '404 Not Found',
        ], $bodyDecoded);
    }
}

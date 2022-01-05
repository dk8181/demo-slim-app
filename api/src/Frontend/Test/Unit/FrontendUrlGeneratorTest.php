<?php

declare(strict_types=1);

namespace App\Frontend\Unit;

use App\Frontend\FrontendUrlGenerator;
use PHPUnit\Framework\TestCase;

/**
 * @covers FrontendUrlGenerator
 */
class FrontendUrlGeneratorTest extends TestCase
{
    public function testEmpty(): void
    {
        $baseUrl = 'http://test.org';
        $generator = $this->getUrlGenerator($baseUrl);

        self::assertEquals($baseUrl, $generator->generate(''));
    }

    public function testPath(): void
    {
        $baseUrl = 'http://test.org';
        $path = 'images';
        $generator = $this->getUrlGenerator($baseUrl);

        // URL = 'http://test.org/images'
        self::assertEquals($baseUrl . '/' . $path, $generator->generate($path));
    }

    public function testParams(): void
    {
        $baseUrl = 'http://test.org';
        $path = 'images';
        $params = ['pic' => 1, 'like' => 100,];

        $generator = $this->getUrlGenerator($baseUrl);

        // URL = 'http://test.org/images?pic=1&like=100'
        self::assertEquals(
            $baseUrl . '/' . $path . '?' . \http_build_query($params),
            $generator->generate($path, $params)
        );
    }

    private function getUrlGenerator(string $baseUrl): FrontendUrlGenerator
    {
        return new FrontendUrlGenerator($baseUrl);
    }
}

<?php

declare(strict_types=1);

namespace App\Frontend\Unit;

use App\Frontend\FrontendUrlGenerator;
use App\Frontend\FrontendUrlTwigExtension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class FrontendUrlTwigExtensionTest extends TestCase
{
    public function testSuccess()
    {
        $frontend = $this->createMock(FrontendUrlGenerator::class);

        $frontend
            ->expects($this->once())
            ->method('generate')
            ->with(
                $this->equalTo('search'),
                $this->equalTo(['a' => 1, 'b' => 2])
            )
            ->willReturn('http://test.org/search?a=1&b=2')
        ;

        $twig = new Environment(
            new ArrayLoader([
                'page.html.twig' => '{{ frontend_url(\'search\', {\'a\': 1,\'b\': 2}) }}',
            ])
        );

        $twig->addExtension(new FrontendUrlTwigExtension($frontend));

        self::assertEquals('http://test.org/search?a=1&amp;b=2', $twig->render('page.html.twig'));
    }
}

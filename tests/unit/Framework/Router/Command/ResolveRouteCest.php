<?php

namespace Test\Framework\Router\Command;

use Pckg\Framework\Router\Command\ResolveRoute;
use Pckg\Framework\Test\Codeception\Cest;
use Pckg\Framework\Test\MockConfig;
use Pckg\Framework\Test\MockExceptions;
use Pckg\Framework\Test\MockRouter;

class ResolveRouteCest
{
    use Cest;
    use MockConfig;
    use MockExceptions;
    use MockRouter;

    public function testDefaultNullMatch()
    {
        $resolveRoute = new ResolveRoute($this->mockRouter(), '/');

        $this->tester->assertNull($resolveRoute->execute());
    }

    public function testStaticRouteMissingView()
    {
        $router = $this->registerRoutes([
            '/' => [],
        ]);
        try {
            $resolveRoute = new ResolveRoute($router, '/');
            $resolveRoute->execute();
            $this->throwException();
        } catch (\Throwable $e) {
            $this->matchException('View not set.', $e);
        }
    }

    public function testMatchesSingle()
    {
        $router = $this->registerRoutes([
            '/' => [
                'view' => fn() => null,
            ],
        ]);
        $resolveRoute = new ResolveRoute($router, '/');
        $match = $resolveRoute->execute();
        $this->tester->assertTrue(isset($match['view']));
        unset($match['view']);
        $expected = ['url' => '/', 'name' => null, 'domain' => null, 'method' => 'GET|POST'];
        $this->tester->assertEquals($expected, array_union($expected, $match));
    }

    public function testMatchesCorrect()
    {
        $router = $this->registerRoutes([
            '/' => [
                'view' => fn() => null,
            ],
            '/foo' => [
                'view' => fn() => null,
            ],
            '/bar' => [
                'view' => fn() => null,
            ],
        ]);
        $resolveRoute = new ResolveRoute($router, '/foo');
        $match = $resolveRoute->execute();
        $this->tester->assertTrue(isset($match['view']));
        unset($match['view']);
        $expected = ['url' => '/foo', 'name' => null, 'domain' => null, 'method' => 'GET|POST'];
        $this->tester->assertEquals($expected, array_union($expected, $match));
    }
}

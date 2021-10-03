<?php

namespace Test\Framework\Reflect;

use Pckg\Auth\Service\Auth;
use Pckg\Framework\Config;
use Pckg\Framework\Helper\Context;
use Pckg\Framework\Reflect\FrameworkResolver;
use Pckg\Framework\Request;
use Pckg\Framework\Request\Data\Flash;
use Pckg\Framework\Response;
use Pckg\Framework\Router;
use Pckg\Framework\Test\Codeception\Cest;
use Pckg\Locale\Lang;
use Pckg\Manager\Asset;
use Pckg\Manager\Locale;
use Pckg\Manager\Meta;
use Pckg\Manager\Seo;

class FrameworkResolverCest
{
    use Cest;

    protected function createFrameworkResolver(): FrameworkResolver
    {
        return new FrameworkResolver();
    }

    public function testCantResolveDefault()
    {
        $frameworkResolver = $this->createFrameworkResolver();
        $this->tester->assertFalse($frameworkResolver->canResolve(static::class), static::class);
    }

    public function testCantResolveNonExistent()
    {
        $frameworkResolver = $this->createFrameworkResolver();
        $this->tester->assertFalse($frameworkResolver->canResolve(HopefullyNonExistentClassOrInterface::class));
    }

    public function testCanResolveSingletones()
    {
        $frameworkResolver = $this->createFrameworkResolver();
        foreach ([
                     Router::class,
                     \Pckg\Concept\Context::class,
                     Config::class,
                     Asset::class,
                     Meta::class,
                     Seo::class,
                     Flash::class,
                     Response::class,
                     Request::class,
                     Lang::class,
                     \Pckg\Auth\Entity\Adapter\Auth::class,
                     Locale::class,
                 ] as $class) {
            $this->tester->assertTrue($frameworkResolver->canResolve($class), $class);
        }
    }

    public function testCanResolveExtendedSingletones()
    {
        $frameworkResolver = $this->createFrameworkResolver();
        foreach ([
                     //Request\MockRequest::class,
                     //Response\MockResponse::class,
                 ] as $class) {
            $this->tester->assertTrue($frameworkResolver->canResolve($class), $class);
        }
    }
}

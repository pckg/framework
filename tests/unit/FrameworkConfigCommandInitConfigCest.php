<?php

use Pckg\Framework\Config\Command\InitConfig;
use Pckg\Framework\Test\ContextDiff;
use Pckg\Framework\Test\ListenForEvents;
use Pckg\Framework\Test\MockConfig;
use Pckg\Framework\Test\MockFramework;
use Pckg\Framework\Test\MockInContext;

class FrameworkConfigCommandInitConfigCest
{

    use MockFramework;
    use ContextDiff;
    use ListenForEvents;
    use MockConfig;
    use MockInContext;

    protected UnitTester $unitTester;

    public function _before(UnitTester $I)
    {
        if (!defined('__ROOT__')) {
            define('__ROOT__', realpath(__DIR__ . '/../..') . '/');
        }
        $this->unitTester = $I;
        $this->mockFramework();
    }

    protected function _after()
    {
    }

    public function testConfig()
    {
        $config = $this->mockConfig();
        $initial = $config->get();
        path('app', __ROOT__ . 'tests/_data/FrameworkConfigCommandInitConfigCest/');
        (new InitConfig($config))->execute(fn() => null);
        $final = $config->get();
        $this->unitTester->assertNotSame($initial, $final);
        $this->unitTester->assertSame(['foo' => 'barconfig', 'url' => 'https://'], $final);
    }
}

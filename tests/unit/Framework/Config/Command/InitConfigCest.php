<?php

namespace Test\Framework\Config\Command;

use Pckg\Framework\Config\Command\InitConfig;
use Pckg\Framework\Test\Codeception\Cest;
use Pckg\Framework\Test\ContextDiff;
use Pckg\Framework\Test\ListenForEvents;
use Pckg\Framework\Test\MockConfig;
use Pckg\Framework\Test\MockInContext;

class InitConfigCest extends Cest
{

    use ContextDiff;
    use ListenForEvents;
    use MockConfig;
    use MockInContext;

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

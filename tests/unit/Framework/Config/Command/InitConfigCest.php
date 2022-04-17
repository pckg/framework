<?php

namespace Test\Framework\Config\Command;

use Pckg\Framework\Config\Command\InitConfig;
use Pckg\Framework\Test\Codeception\Cest;
use Pckg\Framework\Test\ContextDiff;
use Pckg\Framework\Test\ListenForEvents;
use Pckg\Framework\Test\MockConfig;
use Pckg\Framework\Test\MockInContext;

class InitConfigCest
{
    use Cest;
    use ContextDiff;
    use ListenForEvents;
    use MockConfig;
    use MockInContext;

    public function testConfig()
    {
        $config = $this->mockConfig();
        $initial = $config->get();
        $this->tester->assertSame($initial, []);

        path('app', __ROOT__ . 'tests/_data/FrameworkConfigCommandInitConfigCest/');
        (new InitConfig($config))->execute(fn() => null);
        $final = $config->get();
        $this->tester->assertNotSame($initial, $final);

        unset($final['path']);
        $this->tester->assertSame(['foo' => 'barconfig', 'url' => 'https://'], $final);
    }
}

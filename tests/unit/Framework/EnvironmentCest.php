<?php

namespace Test\Framework;

use Pckg\Framework\Config;
use Pckg\Framework\Environment;
use Pckg\Framework\Test\Codeception\Cest;
use Pckg\Framework\Test\ListenForEvents;
use Pckg\Framework\Test\MockContext;

class EnvironmentCest
{
    use Cest;
    use MockContext;
    use ListenForEvents;

    protected $disableFramework = true;

    public function testEnvironment(\UnitTester $I)
    {
        $context = $this->mockContext();
        $config = new Config();

        $I->assertFalse($context->exists(Config::class));

        $environment = new Environment\Production($config, $context);

        $I->assertTrue($context->exists(Config::class));

        $I->assertEquals('/index.php', $environment->getUrlPrefix());
        $I->assertNotEmpty($environment->initArray());

        $this->listenForEvents([Environment::EVENT_INITIALIZING, Environment::EVENT_INITIALIZED]);

        $environment->init();

        $I->assertTrue($this->hasTriggered([Environment::EVENT_INITIALIZING, Environment::EVENT_INITIALIZED]));
    }
}

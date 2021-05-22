<?php

use Pckg\Framework\Test\ContextDiff;
use Pckg\Framework\Test\ListenForEvents;
use Pckg\Framework\Test\MockConfig;
use Pckg\Framework\Test\MockFramework;
use Pckg\Framework\Test\MockInContext;

class FrameworkApplicationCommandRegisterApplicationCest
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

    protected function testRegistersWebsiteApplication()
    {
        $provider = new \Pckg\Framework\Provider();
        $application = new \Pckg\Framework\Application\Website($provider);

        $config = $this->mockConfig();

        $localeManager = $this->mockInContext(new Pckg\Manager\Locale(new \Pckg\Locale\Lang()));

        $this->listenForEvent(\Pckg\Framework\Application::EVENT_REGISTERED);

        $registerApplication = new \Pckg\Framework\Application\Command\RegisterApplication($application, $config);
        $registerApplication->execute(fn() => null);

        $this->unitTester->assertTrue($provider->isRegistered());
        $this->unitTester->assertEquals([], $config->get());
        $this->unitTester->assertEquals('en_GB', $localeManager->getCurrent());
        $this->unitTester->assertEquals(1, $this->getNumberOfTriggers(\Pckg\Framework\Application::EVENT_REGISTERED));
    }
}

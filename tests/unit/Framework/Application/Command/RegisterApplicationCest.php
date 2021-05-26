<?php

namespace Test\Framework\Application\Command;

use Pckg\Framework\Test\Codeception\Cest;
use Pckg\Framework\Test\ContextDiff;
use Pckg\Framework\Test\ListenForEvents;
use Pckg\Framework\Test\MockConfig;
use Pckg\Framework\Test\MockInContext;

class RegisterApplicationCest
{
    use Cest;
    use ContextDiff;
    use ListenForEvents;
    use MockConfig;
    use MockInContext;

    protected function testRegistersWebsiteApplication()
    {
        $provider = new \Pckg\Framework\Provider();
        $application = new \Pckg\Framework\Application\Website($provider);

        $config = $this->mockConfig();

        $localeManager = $this->mockInContext(new Pckg\Manager\Locale(new \Pckg\Locale\Lang()));

        $this->listenForEvent(\Pckg\Framework\Application::EVENT_REGISTERED);

        $registerApplication = new \Pckg\Framework\Application\Command\RegisterApplication($application, $config);
        $registerApplication->execute(fn() => null);

        $this->tester->assertTrue($provider->isRegistered());
        $this->tester->assertEquals([], $config->get());
        $this->tester->assertEquals('en_GB', $localeManager->getCurrent());
        $this->tester->assertEquals(1, $this->getNumberOfTriggers(\Pckg\Framework\Application::EVENT_REGISTERED));
    }
}

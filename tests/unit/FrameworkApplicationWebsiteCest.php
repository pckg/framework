<?php

use Pckg\Concept\Event\Dispatcher;
use Pckg\Framework\Application\Console\Command\RunCommand;
use Pckg\Framework\Request\Data\Server;
use Pckg\Framework\Response;
use Pckg\Framework\Test\ContextDiff;
use Pckg\Framework\Test\ListenForEvents;
use Pckg\Framework\Test\MockConfig;
use Pckg\Framework\Test\MockFramework;
use Pckg\Framework\Test\MockInContext;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\NullOutput;

class FrameworkApplicationWebsiteCest
{

    use MockFramework;
    use ContextDiff;
    use ListenForEvents;
    use MockConfig;
    use MockInContext;

    protected UnitTester $unitTester;

    const EVENT_MIDDLEWARE = self::class . '.middleware';
    const EVENT_AFTERWARE = self::class . '.afterware';

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

    public function testWebsite()
    {
        $provider = new \Pckg\Framework\Provider();
        $website = new \Pckg\Framework\Application\Queue($provider);

        $this->unitTester->assertNotEmpty($website->inits());
        $this->unitTester->assertNotEmpty($website->runs());
    }
}

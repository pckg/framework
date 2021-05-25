<?php

namespace Test\Framework\Application;

use Pckg\Framework\Test\Codeception\Cest;
use Pckg\Framework\Test\ContextDiff;
use Pckg\Framework\Test\ListenForEvents;
use Pckg\Framework\Test\MockConfig;
use Pckg\Framework\Test\MockInContext;

class WebsiteCest extends Cest
{

    use ContextDiff;
    use ListenForEvents;
    use MockConfig;
    use MockInContext;

    const EVENT_MIDDLEWARE = self::class . '.middleware';
    const EVENT_AFTERWARE = self::class . '.afterware';

    public function testWebsite()
    {
        $provider = new \Pckg\Framework\Provider();
        $website = new \Pckg\Framework\Application\Queue($provider);

        $this->tester->assertNotEmpty($website->inits());
        $this->tester->assertNotEmpty($website->runs());
    }
}

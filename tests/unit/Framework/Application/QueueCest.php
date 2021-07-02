<?php

namespace Test\Framework\Application;

use Pckg\Framework\Test\Codeception\Cest;
use Pckg\Framework\Test\ContextDiff;
use Pckg\Framework\Test\ListenForEvents;
use Pckg\Framework\Test\MockConfig;
use Pckg\Framework\Test\MockInContext;

class QueueCest
{
    use Cest;
    use ContextDiff;
    use ListenForEvents;
    use MockConfig;
    use MockInContext;

    const EVENT_MIDDLEWARE = self::class . '.middleware';
    const EVENT_AFTERWARE = self::class . '.afterware';

    public function testQueue()
    {
        $provider = new \Pckg\Framework\Provider();
        $queue = new \Pckg\Framework\Application\Queue($provider);

        $this->tester->assertNotEmpty($queue->inits());
        $this->tester->assertNotEmpty($queue->runs());
    }
}

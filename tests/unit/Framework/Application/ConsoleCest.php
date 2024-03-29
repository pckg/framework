<?php

namespace Test\Framework\Application;

use Pckg\Framework\Test\Codeception\Cest;
use Pckg\Framework\Test\ContextDiff;
use Pckg\Framework\Test\ListenForEvents;
use Pckg\Framework\Test\MockConfig;
use Pckg\Framework\Test\MockInContext;

class ConsoleCest
{
    use Cest;
    use ContextDiff;
    use ListenForEvents;
    use MockConfig;
    use MockInContext;

    const EVENT_MIDDLEWARE = self::class . '.middleware';
    const EVENT_AFTERWARE = self::class . '.afterware';

    public function testConsole()
    {
        $provider = new \Pckg\Framework\Provider();
        $console = new \Pckg\Framework\Application\Console($provider);

        $this->tester->assertNotEmpty($console->inits());
        $this->tester->assertNotEmpty($console->runs());
    }
}

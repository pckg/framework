<?php

namespace Test\Framework\Environment;

use Pckg\Framework\Config;
use Pckg\Framework\Environment;
use Pckg\Framework\Environment\Console;
use Pckg\Framework\Test\Codeception\Cest;
use Pckg\Framework\Test\ListenForEvents;
use Pckg\Framework\Test\MockConfig;

class ConsoleCest
{
    use Cest;
    use MockConfig;
    use ListenForEvents;

    public function testConsoleEnvironmentRegistration()
    {
        $config = $this->mockConfig();

        $this->tester->assertFalse($config->hasRegisteredDir(BASE_PATH));

        $this->listenForEvents([Environment::EVENT_INITIALIZING, Environment::EVENT_INITIALIZED]);

        // this is being tested
        (new Console($config, $this->context))->register();

        $this->tester->assertEquals(E_ALL, error_reporting());
        $this->tester->assertEquals('1', ini_get('display_errors'));

        $this->tester->assertTrue($config->hasRegisteredDir(BASE_PATH));

        $this->tester->assertEquals(1, $this->getNumberOfTriggers(Environment::EVENT_INITIALIZING));
        $this->tester->assertEquals(1, $this->getNumberOfTriggers(Environment::EVENT_INITIALIZED));
    }

    public function testConsoleAppCreation()
    {
        $config = $this->context->get(Config::class);

        try {
            (new Console($config, $this->context))->createApplication($this->context, 'test');
            throw new \Exception('Miss-exception');
        } catch (\Throwable $e) {
            $this->tester->assertEquals('Class Test not found', $e->getMessage());
        }
    }
}

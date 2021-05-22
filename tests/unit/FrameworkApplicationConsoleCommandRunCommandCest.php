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

class FrameworkApplicationConsoleCommandRunCommandCest
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

    public function testEmptyCommandArgv()
    {
        $this->mockInContext(new NullOutput(), RunCommand::class . '.output');
        $dispatcher = $this->mockInContext(new Dispatcher());

        $this->listenForEvent(RunCommand::EVENT_RUNNING);
        $this->listenForEvent(static::EVENT_MIDDLEWARE);
        $this->listenForEvent(static::EVENT_AFTERWARE);

        $response = new Response();
        $response->addMiddleware(function () {
            dispatcher()->trigger(static::EVENT_MIDDLEWARE);
        });
        $response->addAfterware(function () {
            dispatcher()->trigger(static::EVENT_AFTERWARE);
        });

        $symfonyConsole = new Application();
        $symfonyConsole->setAutoExit(false);
        $server = new Server();

        (new RunCommand($response, $dispatcher, $symfonyConsole, $server))->execute(fn() => null);

        $this->unitTester->assertEquals(1, $this->getNumberOfTriggers(RunCommand::EVENT_RUNNING));
        $this->unitTester->assertEquals(1, $this->getNumberOfTriggers(static::EVENT_MIDDLEWARE));
        $this->unitTester->assertEquals(1, $this->getNumberOfTriggers(static::EVENT_AFTERWARE));
    }
}

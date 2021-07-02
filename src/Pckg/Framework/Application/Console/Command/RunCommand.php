<?php

namespace Pckg\Framework\Application\Console\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Concept\Event\Dispatcher;
use Pckg\Framework\Request\Data\Server;
use Pckg\Framework\Response;
use Symfony\Component\Console\Application as SymfonyConsole;
use Symfony\Component\Console\Input\ArgvInput;
use Throwable;

class RunCommand extends AbstractChainOfReponsibility
{

    protected Response $response;

    protected Dispatcher $dispatcher;

    protected SymfonyConsole $symfonyConsole;

    protected Server $server;

    const EVENT_RUNNING = self::class . '.running';

    public function __construct(Response $response, Dispatcher $dispatcher, SymfonyConsole $symfonyConsole, Server $server)
    {
        $this->response = $response;
        $this->dispatcher = $dispatcher;
        $this->symfonyConsole = $symfonyConsole;
        $this->server = $server;
    }

    public function execute(callable $next)
    {
        try {
            /**
             * First argument is always 'console'.
             * Second argument is app name or command.
             * If it's command, we leave things as they are.
             * Id it's app, we unset it.
             */
            $argv = $this->server->get('argv', []);

            /**
             * Remove application name.
             */
            if (isset($argv[1]) && !strpos($argv[1], ':')) {
                unset($argv[1]);
            }

            /**
             * Trigger event
             */
            $this->dispatcher->trigger(static::EVENT_RUNNING, []);

            /**
             * Get Symfony Console Application, find available commands and run app.
             */
            try {
                /**
                 * Apply global middlewares.
                 */
                if ($middlewares = $this->response->getMiddlewares()) {
                    chain($middlewares, 'execute');
                }

                $this->symfonyConsole->run(new ArgvInput(array_values($argv)), context()->getOrDefault(RunCommand::class . '.output'));

                /**
                 * Apply global afterwares/decorators.
                 */
                if ($afterwares = $this->response->getAfterwares()) {
                    chain($afterwares, 'execute', [$this->response]);
                }
            } catch (Throwable $e) {
                die("EXCEPTION: " . \Pckg\Framework\Helper\exception($e));
            }
        } catch (Throwable $e) {
            error_log(\Pckg\Framework\Helper\exception($e));
        }

        return $next();
    }
}

<?php

namespace Pckg\Framework\Application\Console\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Framework\Response;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Application as SymfonyConsole;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class RunCommand extends AbstractChainOfReponsibility
{

    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
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
            $argv = $_SERVER['argv'];

            /**
             * Remove application name.
             */
            if (isset($argv[1]) && !strpos($argv[1], ':')) {
                unset($argv[1]);
            }

            /**
             * Remove platform name.
             */
            if (isset($argv[2]) && !strpos($argv[2], ':') && false === strpos($argv[2], '-')) {
                unset($argv[2]);
            }

            /**
             * Trigger event
             */
            trigger(RunCommand::class . '.running', []);

            /**
             * Get Symfony Console Application, find available commands and run app.
             */
            $application = context()->get(SymfonyConsole::class);
            try {
                /**
                 * Apply global middlewares.
                 */
                if ($middlewares = $this->response->getMiddlewares()) {
                    chain($middlewares, 'execute');
                }

                $application->run(new ArgvInput(array_values($argv)));

                /**
                 * Apply global afterwares/decorators.
                 */
                if ($afterwares = $this->response->getAfterwares()) {
                    chain($afterwares, 'execute', [$this->response]);
                }
            } catch (Throwable $e) {
                die("EXCEPTION: " . exception($e));
            }

            /**
             * This is here just for better readability. =)
             */
            echo "\n";
        } catch (Throwable $e) {

        }

        return $next();
    }

}

<?php

namespace Pckg\Framework\Application\Console\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Framework\Response;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends AbstractChainOfReponsibility
{

    public function execute(callable $next)
    {
        /**
         * First argument is always 'console'.
         * Second argument is app name or command.
         * If it's command, we leave things as they are.
         * Id it's app, we unset it.
         */
        $argv = $_SERVER['argv'];
        if (isset($argv[1]) && !strpos($argv[1], ':')) {
            unset($argv[1]);
            ksort($argv);
        }

        /**
         * Get Symfony Console Application, find available commands and run app.
         */
        $application = context()->get('ConsoleApplication');
        $application->run(new ArgvInput($argv));

        /**
         * This is here just for better readability. =)
         */
        echo "\n";

        return $next();
    }

}

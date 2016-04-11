<?php

namespace Pckg\Framework\Application\Console\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Framework\Console\Migrator;
use Pckg\Framework\Response;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends AbstractChainOfReponsibility
{

    public function execute(callable $next)
    {
        /**
         * First argument is always 'console'.
         * Second argument is app name, we have to unset it.
         */
        unset($_SERVER['argv'][1]);
        ksort($_SERVER['argv']);

        /**
         * Create Symfony Console Application, find available commands and run app.
         */
        $application = new Application();
        $this->findAndAddCommands($application);
        $application->run();

        /**
         * This is here just for better readability. =)
         */
        echo "\n";

        return $next();
    }

    /**
     * T00D00 - This should be registered in console initialization instead of routes!
     *
     * @param Application $application
     */
    public function findAndAddCommands(Application $application)
    {
        $commands = [];
        $classes = get_declared_classes();
        foreach ($classes as $class) {
            if (in_array(Command::class, class_parents($class))) {
                $commands[] = $class;
            }
        }

        $commands[] = Migrator\Run::class;
        $commands[] = Migrator\Show::class;
        foreach ($commands as $command) {
            $application->add(new $command);
        }
    }

}

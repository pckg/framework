<?php namespace Pckg\Framework\Environment;

use Pckg\Concept\Context;
use Pckg\Framework\Environment;
use Whoops\Handler\PlainTextHandler;
use Whoops\Run;
use Symfony\Component\Console\Application as SymfonyConsole;
use Pckg\Concept\Reflect;
use Pckg\Framework\Console\Provider\Console as ConsoleProvider;
use Pckg\Framework\Application;

class Console extends Environment
{

    public function register()
    {
        error_reporting(E_ALL);
        ini_set("display_errors", "1");

        $this->config->parseDir(BASE_PATH);

        $this->registerExceptionHandler();

        $this->init();
    }

    public function registerExceptionHandler()
    {
        $whoops = new Run;
        $whoops->pushHandler(new PlainTextHandler());
        $whoops->register();
    }

    public function createApplication(\Pckg\Framework\Helper\Context $context, $appName)
    {
        /**
         * Examples:
         *  - php console
         *  - php console project:update
         *  - php console derive
         *  - php console derive migrator:install
         */
        $argv = $_SERVER['argv'];
        $commandIndex = null;
        foreach ($argv as $key => $arg) {
            if (strpos($arg, ':')) {
                $commandIndex = $key;
                break;
            }
        }

        $application = null;
        if (!$appName) {
            if (!$commandIndex && isset($argv[1])) {
                $appName = $argv[1];
            } elseif ($commandIndex == 2) {
                $appName = $argv[1];
            } elseif (isset($argv[1]) && $commandIndex > 1) {
                $appName = $argv[1];
            }
        }

        /**
         * We need to set Console Application.
         */
        $context->bind(SymfonyConsole::class, new SymfonyConsole());

        if ($appName) {
            $context->bind('appName', $appName);

            /**
             * Register app paths, autoloaders and create application provider.
             */
            $applicationProvider = $this->registerAndBindApplication($context, $appName);
        } else {
            $applicationProvider = new ConsoleProvider();
        }

        $context->bind(Application::class, $applicationProvider);

        /**
         * Then we create actual application wrapper.
         */
        $application = new Application\Console($applicationProvider);

        return $application;
    }

}
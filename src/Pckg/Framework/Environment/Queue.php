<?php

namespace Pckg\Framework\Environment;

use Pckg\Framework\Application;
use Pckg\Framework\Environment;
use Whoops\Handler\PlainTextHandler;
use Whoops\Run;

class Queue extends Environment
{

    protected $appClass = Application\Queue::class;

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
        $whoops = new Run();
        $whoops->pushHandler(new PlainTextHandler());
        $whoops->register();
    }

    public function createApplication(\Pckg\Framework\Helper\Context $context, $appName)
    {
        /**
         * Register app paths, autoloaders and create application provider.
         */
        $appName = $appName ?? ($_SERVER['argv'][1] ?? null);
        $applicationProvider = $this->registerAndBindApplication($context, $appName);

        /**
         * Bind application to context.
         */
        $context->bind(Application::class, $applicationProvider);

        /**
         * Then we create actual application wrapper.
         */
        $appClass = $this->appClass;
        $application = new $appClass($applicationProvider);

        return $application;
    }
}

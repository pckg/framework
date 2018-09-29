<?php namespace Pckg\Framework\Environment;

use Pckg\Concept\Context;
use Pckg\Framework\Config;
use Pckg\Framework\Environment;
use Whoops\Handler\PlainTextHandler;
use Whoops\Run;

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

}
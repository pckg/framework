<?php namespace Pckg\Framework\Environment;

use Pckg\Concept\Context;
use Pckg\Framework\Config;
use Pckg\Framework\Environment;
use Whoops\Handler\PlainTextHandler;
use Whoops\Run;

class Console extends Environment
{

    function __construct(Config $config, Context $context)
    {
        error_reporting(E_ALL);
        ini_set("display_errors", "1");

        $this->context = $context;

        $this->registerExceptionHandler();

        $context->bind(Config::class, $config);

        $this->init();

        $config->parseDir(path('root'));
    }

    public function registerExceptionHandler()
    {
        $whoops = new Run;
        $whoops->pushHandler(new PlainTextHandler());
        $whoops->register();
    }

}
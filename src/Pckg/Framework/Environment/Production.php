<?php

namespace Pckg\Framework\Environment;

use Pckg\Framework\Config;
use Pckg\Context;
use Pckg\Framework\Environment;
use Pckg\Framework\Environment\Command\DefinePaths;
use Whoops\Run;

class Production extends Environment
{

    public $env = 'pro';

    protected $context;

    protected $initChain = [
        DefinePaths::class,
    ];

    function __construct(Config $config, Context $context)
    {
        error_reporting(null);
        ini_set("display_errors", false);

        $this->context = $context;

        $this->registerExceptionHandler();

        $context->bind('Config', $config);

        $this->init();

        $config->parseDir(path('root'), $this);
    }

    /**
     *
     */
    public function registerExceptionHandler()
    {
        $whoops = new Run;
        $whoops->pushHandler(function ($exception) {
            $this->handleException($exception->getMessage(), $exception->getCode());
        });
        $whoops->register();
    }

    protected function handleException($message, $code)
    {
        $path = realpath(substr(__FILE__, 0, -strlen('Production.php')) . '../Response/') . '/View/';
        if (is_numeric($code) && file_exists($path . $code . '.php')) {
            include $path . $code . '.php';
        }

        include $path . '400.php';
        exit;
    }

}
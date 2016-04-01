<?php

namespace Pckg\Framework\Environment;

use Pckg\Concept\Context;
use Pckg\Framework\Config;
use Pckg\Framework\Environment;
use Rollbar;
use Whoops\Run;

class Production extends Environment
{

    public $env = 'pro';

    protected $context;

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
            Rollbar::init(array('access_token' => 'd0d3d181ed0d4430b73bc46ed8dc8b98', 'report_suppressed' => true));
            Rollbar::report_exception($exception);

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
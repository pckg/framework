<?php

namespace Pckg\Framework\Environment;

use Exception;
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

        $context->bind(Config::class, $config);

        $this->init();

        $config->parseDir(path('root'), $this);
    }

    /**
     *
     */
    public function registerExceptionHandler()
    {
        $whoops = new Run;
        $whoops->pushHandler(
            function($exception) {
                Rollbar::init(
                    [
                        'access_token'      => config('rollbar.access_token', 'd0d3d181ed0d4430b73bc46ed8dc8b98'),
                        'report_suppressed' => config('rollbar.report_suppressed', true),
                    ]
                );
                Rollbar::report_exception($exception);

                $this->handleException($exception);
            }
        );
        $whoops->register();
    }

    protected function handleException(Exception $e)
    {
        $code = $e->getCode();
        $message = $e->getMessage();

        $path = realpath(substr(__FILE__, 0, -strlen('Production.php')) . '../Response/') . '/View/';
        if (is_numeric($code) && file_exists($path . $code . '.php')) {
            include $path . $code . '.php';
        }

        include $path . '400.php';
        exit;
    }

}
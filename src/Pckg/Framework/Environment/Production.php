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
                $whitelist = config('rollbar.whitelist');

                if (config('rollbar.access_token') && $whitelist()) {
                    Rollbar::init(
                        [
                            'access_token'      => config('rollbar.access_token'),
                            'report_suppressed' => config('rollbar.report_suppressed', true),
                        ]
                    );
                    Rollbar::report_exception($exception);
                }

                $this->handleException($exception);
            }
        );
        $whoops->register();
    }

    protected function handleException(Exception $e)
    {
        response()->code(404);
        $code = $e->getCode() ? $e->getCode() : response()->getCode();
        $message = $e->getMessage();

        $handled = false;
        do {
            try {
                $response = view(
                    'Pckg\Framework:error/' . $code,
                    [
                        'message' => $message,
                        'code'    => $code,
                    ]
                )->autoparse();

                if ($response) {
                    $handled = true;
                }
            } catch (Exception $e) {
            }
        } while (!$handled);

        if ($handled) {
            echo $response;
        } else {
            echo $code . ' : ' . $message;
        }

        exit;
    }

}
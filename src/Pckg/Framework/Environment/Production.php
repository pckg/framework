<?php

namespace Pckg\Framework\Environment;

use Derive\Layout\Provider\DeriveAssets;
use Exception;
use Pckg\Concept\Context;
use Pckg\Concept\Reflect;
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

                if (config('rollbar.access_token') && Reflect::call($whitelist)) {
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

        if (class_exists(DeriveAssets::class)) {
            Reflect::create(DeriveAssets::class)->register();
        }

        $handled = false;
        $codes = [$code, 'default'];
        foreach ($codes as $file) {
            try {
                $response = view(
                    'Pckg\Framework:error/' . $file,
                    [
                        'message' => $message,
                        'code'    => $code,
                    ]
                )->autoparse();

                if ($response) {
                    $handled = true;
                    break;
                }
            } catch (Exception $e) {
                dd(exception($e));
            }
        }

        if ($handled) {
            echo $response;
        } else {
            echo $code . ' : ' . $message;
        }

        exit;
    }

}
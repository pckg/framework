<?php

namespace Pckg\Framework\Environment;

use Pckg\Concept\Context;
use Pckg\Concept\Reflect;
use Pckg\Framework\Config;
use Pckg\Framework\Environment;
use Pckg\Framework\Exception;
use Rollbar\Payload\Level;
use Throwable;
use Whoops\Run;
use Rollbar\Rollbar;

class Production extends Environment
{

    public $env = 'pro';

    protected $context;

    function __construct(Config $config, Context $context)
    {
        error_reporting(0);
        ini_set("display_errors", 0);

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
                            'reportSuppressed'  => config('rollbar.reportSuppressed', true),
                            'environment'       => 'production',
                            'root'              => path('root'),
                        ]
                    );
                    $level = Level::ERROR;
                    if (response()->code() == 500) {
                        $level = Level::CRITICAL;
                    } else if (in_array(response()->code(), [400, 401, 402, 403, 404])) {
                        $level = Level::WARNING;
                    }
                    Rollbar::log($level, $exception);
                }

                $this->handleException($exception);
            }
        );
        $whoops->register();
    }

    protected function handleException(Throwable $e)
    {
        if (response()->getCode() == 200) {
            response()->code(404);
        }
        $code = $e->getCode() ? $e->getCode() : response()->getCode();
        $message = $e->getMessage();

        $handled = false;
        $codes = [/*$code, */'default'];
        foreach ($codes as $file) {
            try {
                $response = view(
                    'Pckg/Framework:error/' . $file,
                    [
                        'message'   => $message,
                        'code'      => $code,
                        'exception' => $e,
                    ]
                )->autoparse();

                if (!$response) {
                    continue;
                }

                $handled = true;
                break;
            } catch (Throwable $e) {
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
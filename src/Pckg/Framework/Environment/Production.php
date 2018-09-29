<?php

namespace Pckg\Framework\Environment;

use Pckg\Concept\Context;
use Pckg\Concept\Reflect;
use Pckg\Framework\Config;
use Pckg\Framework\Environment;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;
use Throwable;
use Whoops\Run;

class Production extends Environment
{

    public $env = 'pro';

    public function register()
    {
        error_reporting(0);
        ini_set("display_errors", 0);

        $this->config->parseDir(BASE_PATH);

        $this->registerExceptionHandler();
        //throw new \Exception('Test prexception');

        $this->init();
    }

    /**
     *
     */
    public function registerExceptionHandler()
    {
        $whoops = new Run();
        $whoops->pushHandler(function($exception) {
            /**
             * Change error to 500 on successful response.
             */
            if (response()->getCode() == 200) {
                response()->code(500);
            }

            /**
             * Try report to rollbar.
             */
            $this->reportToRollbar();

            /**
             * Respond to client.
             */
            $this->handleException($exception);
        });

        /**
         * Register whoops as exception handler.
         */
        $whoops->register();
    }

    public function reportToRollbar()
    {
        try {
            $whitelist = config('rollbar.whitelist');
            if (config('rollbar.access_token') && $whitelist && Reflect::call($whitelist)) {
                Rollbar::init([
                                  'access_token'     => config('rollbar.access_token'),
                                  'reportSuppressed' => config('rollbar.reportSuppressed', true),
                                  'environment'      => 'production',
                                  'root'             => path('root'),
                              ]);
                $level = Level::ERROR;
                if (response()->getCode() == 500) {
                    $level = Level::CRITICAL;
                } elseif (in_array(response()->getCode(), [400, 401, 402, 403, 404])) {
                    $level = Level::WARNING;
                }
                Rollbar::log($level, $exception);
            }
        } catch (Throwable $e) {
            error_log('Exception reporting to rollbar: ' . $e->getMessage());
        }
    }

    protected function handleException(Throwable $e)
    {
        try {
            $code = $e->getCode() ? $e->getCode() : response()->getCode();
            $message = $e->getMessage();

            $handled = false;
            $codes = [/*$code, */
                      'default',
            ];
            foreach ($codes as $file) {
                try {
                    $response = view(config('pckg.framework.errorTemplateDir',
                                            'vendor/pckg/generic/src/Pckg/Generic/View/error/') . $file,
                                     [
                                         'message'   => $message,
                                         'code'      => $code,
                                         'exception' => $e,
                                     ])->autoparse();

                    if (!$response) {
                        continue;
                    }

                    $handled = true;
                    break;
                } catch (Throwable $e) {
                    // slowly die
                }
            }

            if ($handled) {
                echo $response;
            } else {
                echo $message . ' (' . $code . ')';
            }
        } catch (Throwable $e) {
            // slowly die
            die("E");
        }

        exit;
    }

}
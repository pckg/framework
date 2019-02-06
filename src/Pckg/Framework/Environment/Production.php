<?php

namespace Pckg\Framework\Environment;

use Pckg\Concept\Context;
use Pckg\Concept\Reflect;
use Pckg\Framework\Application;
use Pckg\Framework\Application\Website;
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
            $this->reportToRollbar($exception);

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

    public function reportToRollbar(Throwable $exception)
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
                if (in_array(response()->getCode(), [500, 200])) {
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

            @error_log($message . ' (' . $code . ')');

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
                                         'message'   => $e->getMessage(),
                                     ])->autoparse();

                    if (!$response) {
                        continue;
                    }

                    echo $response;
                    exit;
                } catch (Throwable $e) {
                    if (implicitDev()) {
                        echo '<p>' . exception($e) . '</p>';
                    }
                    @error_log($e->getMessage());
                    // slowly die
                }
            }

            /**
             * @T00D00 - add nice html response?
             */
            $response = '<html><head><title>Service is temporarily unavailable</title></head><body><p>Service is temporarily unavailable</p></body></html>';

            echo $response;

            if (!$handled && implicitDev()) {
                echo '<p>' . exception($e) . '</p>';
            }
        } catch (Throwable $e) {
            @error_log('Error handling exception: ' . $e->getMessage());
        }

        exit;
    }

    public function getApplicationNameFromGlobalRouter()
    {
        $apps = config('router.apps');

        foreach ($apps as $app => $config) {
            if (in_array($_SERVER['HTTP_HOST'], $config['host'])) {
                return $app;
            }

            if (isset($config['callable']) && $config['callable']) {
                return $app;
            }

            foreach ($config['host'] as $host) {
                if (strpos($host, '(') !== false && preg_match('/' . $host . '/', $_SERVER['HTTP_HOST'])) {
                    return $app;
                }
            }
        }
    }

    public function createApplication(\Pckg\Framework\Helper\Context $context, $appName)
    {
        if (!($appName = $this->getApplicationNameFromGlobalRouter())) {
            throw new \Exception('Cannot fetch app from global router.');
        }

        /**
         * Register app paths, autoloaders and create application provider.
         */
        $applicationProvider = $this->registerAndBindApplication($context, $appName);

        /**
         * Bind application to context.
         */
        $context->bind(Application::class, $applicationProvider);

        /**
         * Then we create actual application wrapper.
         */
        $application = new Website($applicationProvider);

        return $application;
    }

}
<?php

namespace Pckg\Framework\Environment;

use Pckg\Concept\Reflect;
use Pckg\Framework\Application;
use Pckg\Framework\Application\Website;
use Pckg\Framework\Environment;
use Pckg\Framework\Exception\NotFound;
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
        ini_set("display_errors", '0');

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
        $whoops->pushHandler(function ($exception) {
            /**
             * Change error to 500 on successful response.
             */
            if (response()->getCode() == 200) {
                response()->code(500);
            }

            /**
             * Try report to rollbar.
             * Respond to client.
             * @T00D00 - emit event and report in listener
             */
            $this->reportToRollbar($exception);
            $this->handleException($exception);
        });

        /**
         * Register whoops as exception handler.
         */
        $whoops->register();
    }

    public function reportToRollbar(Throwable $exception)
    {
        if (is_subclass_of($exception, NotFound::class)) {
            error_log('Not found: ' . exception($exception));
            return;
        }

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
            $request = request();
            $response = response();

            $code = $e->getCode() ? $e->getCode() : $response->getCode();
            $message = $e->getMessage();
            $response->sendCodeHeader();

            @error_log(exception($e) . ' (' . $code . ')');

            /**
             * Handle JSON request.
             */
            if ($request->isJson() || $request->isAjax() || $request->isCORS()) {
                $response = [
                    'success' => false,
                    'error' => true,
                    'message' => $message,
                    'statusCode' => $response->getCode(),
                    'exception' => implicitDev() ? exception($e) : null,
                ];

                response()->respond($response);
                exit;
            }

            $handled = false;
            $codes = [/*$code, */
                      'default',
            ];
            foreach ($codes as $file) {
                try {
                    $view = config('pckg.framework.errorTemplateDir', 'vendor/pckg/generic/src/Pckg/Generic/View/error/') . $file;
                    $output = view(
                        $view,
                        [
                                         'message'   => $message,
                                         'code'      => $code,
                                         'exception' => $e,
                        ]
                    )->autoparse();

                    if (!$output) {
                        continue;
                    }

                    echo $output;
                    exit;
                } catch (Throwable $e) {
                    @error_log($e->getMessage());
                    // slowly die
                }
            }

            /**
             * @T00D00 - add nice html response?
             */
            $output = '<html><head><title>Service is temporarily unavailable</title></head><body><p>Service is temporarily unavailable</p></body></html>';

            echo $output;
        } catch (Throwable $e) {
            @error_log('Error handling exception: ' . $e->getMessage());
        }

        exit;
    }

    public function getApplicationNameFromGlobalRouter()
    {
        $apps = config('router.apps', []);
        $hostname = $_SERVER['HTTP_HOST'] ?? null;

        foreach ($apps as $app => $config) {
            if (isset($config['path'])) {
                if (!in_array($_SERVER['SCRIPT_URL'], $config['path'])) {
                    continue;
                }
            }
            if (in_array($hostname, $config['host'])) {
                return $app;
            }

            if (isset($config['callable']) && $config['callable']) {
                return $app;
            }

            foreach ($config['host'] as $host) {
                if (strpos($host, '(') !== false && preg_match('/' . $host . '/', $hostname)) {
                    return $app;
                }
            }
        }

        return config('app');
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

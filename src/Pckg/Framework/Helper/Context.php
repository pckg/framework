<?php

namespace Pckg\Framework\Helper;

use Exception;
use Pckg\Concept\Context as ConceptContext;
use Pckg\Concept\Reflect;
use Pckg\Framework\Application;
use Pckg\Framework\Application\Console;
use Pckg\Framework\Provider\Helper\Registrator;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\ArgvInput;

class Context extends ConceptContext
{

    use Registrator;

    public function createEnvironment($environment)
    {
        $this->bind('Environment', Reflect::create($environment));

        return $this;
    }

    /**
     * @param null $appName
     *
     * @return Application
     * @throws Exception
     */
    public function createApplication($appName = null)
    {
        if (!$appName) {
            if (isset($_SERVER['APP'])) {
                $appName = $_SERVER['APP'];
            } else {
                $appName = $this->getApplicationNameFromGlobalRouter();
            }
        }

        if (!$appName) {
            throw new Exception('$appName undefined');
        }

        path('app', path('root') . "app" . path('ds') . strtolower($appName) . path('ds'));
        path('app_src', path('app') . "src" . path('ds'));

        /**
         * Add app src dir to autoloader and template engine.
         */
        $this->registerAutoloaders(path('app_src'), $this);

        /**
         * Now we will be able to create and register application.
         */
        $app = Reflect::create(ucfirst($appName));

        $this->bind('Application', $app);

        return $app;
    }

    /**
     * @return Console
     * @throws Exception
     */
    public function createConsoleApplication()
    {
        $application = null;
        if (isset($_SERVER['argv'][1]) && !strpos($_SERVER['argv'][1], ':')) {
            $appName = $_SERVER['argv'][1];
            path('app', path('root') . "app" . path('ds') . strtolower($appName) . path('ds'));
            path('app_src', path('app') . "src" . path('ds'));

            /**
             * Add app src dir to autoloader and template engine.
             */
            $this->registerAutoloaders(path('app_src'), $this);

            /**
             * Now we will be able to create and register application.
             */
            $application = Reflect::create(ucfirst($appName));
            $this->bind('Application', $application);
        }

        $console = new Console($application);
        $this->bind('Application', $application ?: $console);

        /**
         * We also need to set Console Application.
         */
        $consoleApplication = new ConsoleApplication();
        $this->bind('ConsoleApplication', $consoleApplication);
        
        return $console;
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
                if (preg_match('/' . $host . '/', $_SERVER['HTTP_HOST'])) {
                    return $app;
                }
            }
        }
    }

}
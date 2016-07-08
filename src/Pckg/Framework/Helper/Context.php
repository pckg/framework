<?php namespace Pckg\Framework\Helper;

use Exception;
use Pckg\Concept\Context as ConceptContext;
use Pckg\Concept\Reflect;
use Pckg\Framework\Application;
use Pckg\Framework\Application\Console;
use Pckg\Framework\Application\Website;
use Pckg\Framework\Console\Provider\Console as ConsoleProvider;
use Pckg\Framework\Environment;
use Pckg\Framework\Provider;
use Pckg\Framework\Provider\Helper\Registrator;
use Symfony\Component\Console\Application as SymfonyConsole;

class Context extends ConceptContext
{

    use Registrator;

    public function createEnvironment($environment)
    {
        $this->bind(Environment::class, Reflect::create($environment));

        return $this;
    }

    public function createWebsiteApplication()
    {
        if (!($appName = $this->getApplicationNameFromGlobalRouter())) {
            throw new Exception('Cannot fetch app from global router.');
        }

        path('app', path('root') . "app" . path('ds') . strtolower($appName) . path('ds'));
        path('app_src', path('app') . "src" . path('ds'));

        /**
         * Add app src dir to autoloader and template engine.
         */
        $this->registerAutoloaders(path('app_src'), $this);

        /**
         * Now we will be able to create and register application provider.
         */
        $applicationProvider = Reflect::create(ucfirst($appName));

        $this->bind(Application::class, $applicationProvider);

        /**
         * Then we create actual application wrapper.
         */
        $application = new Website($applicationProvider);

        return $application;
    }

    public function createConsoleApplication($appName = null)
    {
        $application = null;
        if (!$appName) {
            if (isset($_SERVER['argv'][1]) && !strpos($_SERVER['argv'][1], ':')) {
                $appName = $_SERVER['argv'][1];
            }
        }

        /**
         * We need to set Console Application.
         */
        $this->bind(SymfonyConsole::class, new SymfonyConsole());

        if ($appName) {
            path('app', path('root') . "app" . path('ds') . strtolower($appName) . path('ds'));
            path('app_src', path('app') . "src" . path('ds'));

            /**
             * Add app src dir to autoloader and template engine.
             */
            $this->registerAutoloaders(path('app_src'), $this);

            /**
             * Now we will be able to create and register application provider.
             */
            $applicationProvider = Reflect::create(ucfirst($appName));

            /**
             * We register console provider so consoles can be easily accessable.
             */
            (new ConsoleProvider())->register();
        } else {
            $applicationProvider = new ConsoleProvider();
        }

        $this->bind(Application::class, $applicationProvider);

        /**
         * Then we create actual application wrapper.
         */
        $application = new Console($applicationProvider);

        return $application;
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
<?php

namespace Pckg\Framework\Helper;

use Exception;
use Pckg\Concept\Context as ConceptContext;
use Pckg\Concept\Reflect;
use Pckg\Framework\Application;
use Pckg\Framework\Application\Console;
use Pckg\Framework\Application\Website;
use Pckg\Framework\Environment;
use Pckg\Framework\Provider\Helper\Registrator;
use Symfony\Component\Console\Application as SymfonyConsole;
use Symfony\Component\Console\Input\ArgvInput;

class Context extends ConceptContext
{

    use Registrator;

    public function createEnvironment($environment)
    {
        $this->bind(Environment::class, Reflect::create($environment));

        return $this;
    }

    /**
     * @param null $appName
     *
     * @return Application
     * @throws Exception
     */
    public function createApplication($parentApplication, $appName = null)
    {
        $application = null;
        if ($parentApplication == Console::class) {
            if (!$appName) {
                if (isset($_SERVER['argv'][1]) && !strpos($_SERVER['argv'][1], ':')) {
                    $appName = $_SERVER['argv'][1];
                }
            }

            /**
             * We need to set Console Application.
             */
            $this->bind(SymfonyConsole::class, new SymfonyConsole());

        } elseif ($parentApplication == Website::class)  {
            if (!$appName) {
                if (isset($_SERVER['APP'])) {
                    $appName = $_SERVER['APP'];
                } else {
                    $appName = $this->getApplicationNameFromGlobalRouter();
                }
            }
        } else {
            throw new Exception('Unknown parent application');
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
        $application = new $parentApplication($applicationProvider);

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
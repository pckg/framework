<?php

namespace Pckg\Framework\Helper;

use Exception;
use Pckg\Concept\Context as ConceptContext;
use Pckg\Concept\Reflect;
use Pckg\Framework\Application;
use Pckg\Framework\Environment\Production;
use Pckg\Framework\Provider\Helper\Registrator;

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

        $this->registerAutoloaders(path('app_src'), $this);

        /**
         * On this point we have registered autoloader
         * so now we can create and register application.
         */
        $app = Reflect::create(ucfirst($appName));

        $this->bind('Application', $app);

        return $app;
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
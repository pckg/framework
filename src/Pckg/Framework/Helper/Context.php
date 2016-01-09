<?php

namespace Pckg\Framework\Helper;

use Pckg\Framework\Provider\Helper\AutoloaderRegistrator;

class Context extends \Pckg\Concept\Context
{

    use AutoloaderRegistrator;

    public function createEnvironment($environment)
    {
        $this->bind('Environment', Reflect::create($environment));

        return $this;
    }

    public function createApplication($appName = null)
    {
        if (!$appName) {
            $appName = $_SERVER['APP'];
        }

        path('app', path('root') . "app" . path('ds') . $appName . path('ds'));
        path('app_src', path('app') . "src" . path('ds'));

        $this->registerAutoloaders([path('app_src')]);

        /**
         * On this point we have registered autoloader
         * so now we can create and register application.
         */
        $app = Reflect::create(ucfirst($appName));

        $this->bind('Application', $app);

        return $app;
    }

    public function getOrCreate($key, $class, $args = [])
    {
        if (!isset($this->data[$key])) {
            $this->data[$key] = Reflect::create($class, $args);
        }

        return $this->data[$key];
    }

    public static function autorun($environment, $application = null)
    {
        static::createInstance()
            ->createEnvironment($environment)
            ->createApplication($application)
            ->init()
            ->run();
    }

}
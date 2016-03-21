<?php

namespace Pckg\Framework\Helper;

use Exception;
use Pckg\Concept\Reflect;
use Pckg\Framework\Application;
use Pckg\Framework\Provider\Helper\Registrator;

class Context extends \Pckg\Concept\Context
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

        path('app', path('root') . "app" . path('ds') . $appName . path('ds'));
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
            if (in_array($_SERVER['HTTP_HOST'], $config['domains'])) {
                return $app;
            } else if (isset($config['callable']) && $config['callable']) {
                return $app;
            }
        }
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
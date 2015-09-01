<?php

namespace Pckg\Framework\Helper;

use Pckg\Framework\Application\Website;

class Context extends \Pckg\Concept\Context {

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

        autoloader()->add('', path('app_src'));

        $appClass = ucfirst($appName);
        $app = class_exists($appClass) ? Reflect::create($appClass, $appName) : new Website($appName);

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

}
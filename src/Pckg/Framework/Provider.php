<?php

namespace Pckg\Framework;

use Pckg\Concept\Reflect;
use Pckg\Framework\Provider\Helper\Registrator;

class Provider
{

    use Registrator;

    /**
     * Register options
     */
    public function register()
    {
        $this->registerApps($this->apps());
        $this->registerProviders($this->providers());
        $this->registerRoutes($this->routes());
        $this->registerListeners($this->listeners());
        $this->registerMiddlewares($this->middlewares());
        $this->registerAfterwares($this->afterwares());
        $this->registerPaths($this->paths());
        $this->registerViewObjects($this->viewObjects());
        $this->registerConsoles($this->consoles());

        /**
         * Those assets are always added.
         * We need to find a way how to register assets conditionally.
         */
        $this->registerAssets($this->assets());

        if (method_exists($this, 'registered')) {
            Reflect::method($this, 'registered');
        }
    }

    protected function getViewPaths()
    {
        $db = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        $file = $db[0]['file'];
        $class = $db[1]['class'];

        return [
            realpath(substr($file, 0, strrpos($file, path('ds'))) . path('ds') . '..' . path('ds') . 'View'),
            substr($file, 0, -strlen($class) - strlen('.php')),
        ];
    }

    public function apps()
    {
        return [];
    }

    public function providers()
    {
        return [];
    }

    public function routes()
    {
        return [];
    }

    public function middlewares()
    {
        return [];
    }

    public function afterwares()
    {
        return [];
    }

    public function paths()
    {
        return [];
    }

    public function consoles()
    {
        return [];
    }

    public function assets()
    {
        return [];
    }

    public function viewObjects()
    {
        return [];
    }

    public function listeners()
    {
        return [];
    }

}
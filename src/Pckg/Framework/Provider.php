<?php

namespace Pckg\Framework;

use Pckg\Concept\Reflect;
use Pckg\Framework\Application\ApplicationInterface;
use Pckg\Framework\Provider\Helper\Registrator;
use Pckg\Manager\Asset as AssetManager;

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
        $this->registerMiddlewares($this->middlewares());
        $this->registerPaths($this->paths());
        $this->registerConsoles($this->consoles());
        $this->registerAssets($this->assets());

        if (method_exists($this, 'registered')) {
            Reflect::method($this, 'registered');
        }
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

}
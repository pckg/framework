<?php

namespace Pckg\Framework;

use Pckg\Concept\Reflect;
use Pckg\Framework\Application\ApplicationInterface;
use Pckg\Framework\Router\Helper\RouteRegistrator;
use Pckg\Manager\Asset as AssetManager;

class Provider
{

    use RouteRegistrator;

    protected $app;

    protected $assetManager;

    function __construct(ApplicationInterface $app, AssetManager $assetManager)
    {
        $this->app = $app;
        $this->assetManager = $assetManager;
    }

    public function registerConsoleApplication()
    {
        $this->registerMiddlewares($this->middlewares());
        $this->registerPaths($this->paths());
        $this->registerConsoles($this->consoles());
    }

    public function registerWebApplication()
    {
        $this->registerRoutes($this->routes());
        $this->registerMiddlewares($this->middlewares());
        $this->registerPaths($this->paths());
        $this->registerAssets($this->assets());
    }

    /**
     * Register options
     */
    public function register()
    {
        if (isConsole()) {
            $this->registerConsoleApplication();

        } else {
            $this->registerWebApplication();
            
        }

        if (method_exists($this, 'registered')) {
            Reflect::method($this, 'registered');
        }
    }

    /**
     * Register routes
     *
     * @return array
     */
    public function routes()
    {
        return [];
    }

    /**
     * Register middlewares
     *
     * @return array
     */
    public function middlewares()
    {
        return [];
    }

    public function registerMiddlewares($middlewares)
    {
        // @T00D00
    }

    /**
     * Register view paths.
     *
     * @return array
     */
    public function paths()
    {
        return [];
    }

    public function registerPaths($paths)
    {
        // @T00D00
    }

    /**
     * Register assets
     *
     * @return array
     */
    public function assets()
    {
        return [];
    }

    public function registerAssets($assets)
    {
        foreach ($assets as $key => $assets) {
            $this->assetManager->addProviderAssets($assets, is_array($assets) ? $key : 'main', $this);
        }
    }

    public function consoles()
    {
        return [];
    }

    public function registerConsoles($consoles)
    {
        // T00D00
        foreach ($consoles as $console) {

        }
    }

}
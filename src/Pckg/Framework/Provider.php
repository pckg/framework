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

    public function register()
    {
        $this->registerRoutes($this->routes());
        $this->assetManager->addProviderAssets($this->assets(), 'main', $this);
    }

    public function commands()
    {
        return [];
    }

    public function controllers()
    {
        return [];
    }

    public function middlewares()
    {
        return [];
    }

    public function path()
    {
        return [];
    }

    public function routes()
    {
        return [];
    }

    public function assets()
    {
        return [];
    }

}
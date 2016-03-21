<?php

namespace Pckg\Framework;

use Pckg\Concept\Reflect;
use Pckg\Framework\Application\ApplicationInterface;
use Pckg\Framework\Router\Helper\RouteRegistrator;

class Provider
{

    use RouteRegistrator;

    function __construct(ApplicationInterface $app)
    {
        $this->app = $app;
    }

    public function register()
    {
        $this->registerRoutes($this->routes());
        $this->registerMiddlewares();
    }

    protected function registerMiddlewares()
    {
        foreach ($this->middlewares() as $middleware) {
            $this->app->addMiddleware(Reflect::create($middleware));
        }
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

}
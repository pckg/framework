<?php

namespace Pckg;

class PackageProvider
{

    function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function register()
    {
        foreach ($this->middleware() as $middleware) {
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

    public function middleware()
    {
        return [];
    }

    public function path()
    {
        return [];
    }

}
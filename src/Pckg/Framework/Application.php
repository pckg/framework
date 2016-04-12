<?php

namespace Pckg\Framework;

use Pckg\Framework\Application\ApplicationInterface;
use Pckg\Framework\Application\Website\Command\Init;
use Pckg\Framework\Application\Website\Command\Run;
use Pckg\Framework\Provider\AutoloaderManager;
use Pckg\Framework\Provider\Helper\Registrator;
use Pckg\Framework\Provider\ProviderManager;
use Pckg\Framework\Router\RouterManager;

class Application implements ApplicationInterface, ProviderManager, AutoloaderManager, RouterManager
{

    use Registrator;

    public function init()
    {
        chain($this->inits(), 'execute', [$this]);

        return $this;
    }

    public function inits()
    {
        return [
            Init::class,
        ];
    }

    public function run()
    {
        $this->middleware();

        chain($this->runs(), 'execute', [$this]);

        return $this;
    }

    public function runs()
    {
        return [
            Run::class,
        ];
    }

    public function middleware()
    {
        chain($this->middlewares(), 'execute', [$this]);

        return $this;
    }

    public function middlewares()
    {
        return [];
    }

    public function providers()
    {
        return [];
    }

    public function autoload()
    {
        return [];
    }

    public function routes()
    {
        return [];
    }

}
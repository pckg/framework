<?php

namespace Pckg\Framework;

use Pckg\Concept\Middleware;
use Pckg\Framework\Application\ApplicationInterface;
use Pckg\Framework\Application\Website\Command\Init;
use Pckg\Framework\Application\Website\Command\Run;
use Pckg\Framework\Provider\AutoloaderManager;
use Pckg\Framework\Provider\Helper\Registrator;
use Pckg\Framework\Provider\ProviderManager;

class Application implements ApplicationInterface, ProviderManager, AutoloaderManager
{

    use Registrator, Middleware;

    public function init()
    {
        chain($this->initArray(), 'execute', [$this]);

        return $this;
    }

    public function initArray()
    {
        return [
            Init::class,
        ];
    }

    public function run()
    {
        $this->middleware();

        chain($this->runArray(), 'execute', [$this]);

        return $this;
    }

    public function runArray()
    {
        return [
            Run::class,
        ];
    }

    public function providers()
    {
        return [];
    }

    public function autoload()
    {
        return [];
    }

}
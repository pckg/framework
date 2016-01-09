<?php

namespace Pckg\Framework;

use Pckg\Concept\Middleware;
use Pckg\Framework\Application\ApplicationInterface;
use Pckg\Framework\Application\Website\Command\Run;
use Pckg\Framework\Application\Website\Command\Init;
use Pckg\Framework\Provider\AutoloaderManager;
use Pckg\Framework\Provider\Helper\AutoloaderRegistrator;
use Pckg\Framework\Provider\Helper\ProviderRegistrator;
use Pckg\Framework\Provider\ProviderManager;

class Application implements ApplicationInterface, ProviderManager, AutoloaderManager
{

    use ProviderRegistrator, AutoloaderRegistrator;

    use Middleware;

    protected $mapper = [];

    public function init()
    {
        chain([Init::class], 'execute', [$this]);

        return $this;
    }

    public function run()
    {
        $this->middleware();

        chain([Run::class], 'execute', [$this]);

        return $this;
    }

    /**
     * @deprecated
     */
    public function getMapped($key, $second)
    {
        return isset($this->mapper[$key][$second])
            ? $this->mapper[$key][$second]
            : null;
    }

    public function providers()
    {
        return [];
    }

    public function autoload()
    {
        return [];
    }

    public function afterAutoload()
    {
        return $this;
    }

}
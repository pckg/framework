<?php

namespace Pckg\Framework;

use Pckg\Framework\Application\ApplicationInterface;
use Pckg\Framework\Provider\Helper\Registrator;

class Application
{

    use Registrator;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    public function getProvider()
    {
        return $this->provider;
    }

    public function init()
    {
        trigger(Application::class . '.initializing', [$this]);

        $init = chain($this->inits(), 'execute', [$this]);

        if (!$init) {
            throw new \Exception('Error initializing application');
        }

        trigger(Application::class . '.initialized', [$this]);

        return $this;
    }

    public function run()
    {
        trigger(Application::class . '.running', [$this]);

        $run = chain($this->runs(), 'execute', [$this]);

        if (!$run) {
            throw new \Exception('Error running application');
        }

        trigger(Application::class . '.ran', [$this]);

        return $this;
    }

}
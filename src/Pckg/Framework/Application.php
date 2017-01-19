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
        trigger('application.initializing', [$this]);

        chain($this->inits(), 'execute', [$this]);

        trigger('application.initialized', [$this]);

        return $this;
    }

    public function run()
    {
        trigger('application.running', [$this]);

        chain($this->runs(), 'execute', [$this]);

        trigger('application.ran', [$this]);

        return $this;
    }

}
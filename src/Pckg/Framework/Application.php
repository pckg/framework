<?php

namespace Pckg\Framework;

use Pckg\Framework\Application\ApplicationInterface;
use Pckg\Framework\Provider\Helper\Registrator;

class Application implements ApplicationInterface
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
        chain($this->inits(), 'execute', [$this]);

        return $this;
    }

    public function run()
    {
        chain($this->runs(), 'execute', [$this]);

        return $this;
    }

}
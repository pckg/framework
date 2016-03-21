<?php

namespace Pckg\Framework\Provider\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Framework\Application;
use Pckg\Framework\Provider\Helper\Registrator;
use Pckg\Framework\Provider\ProviderManager;

class InitProviders extends AbstractChainOfReponsibility
{

    use Registrator;

    protected $manager;

    public function __construct(ProviderManager $manager)
    {
        $this->manager = $manager;
    }

    public function execute(callable $next)
    {
        $this->registerProviders($this->manager->providers(), $this->manager);

        return $next();
    }

}
<?php

namespace Pckg\Framework\Config\Command;

use Pckg\Concept\AbstractChainOfReponsibility;

use Pckg\Concept\Context;
use Pckg\Concept\Reflect;
use Pckg\Framework\Config;
use Pckg\Framework\Router;

class InitConfig extends AbstractChainOfReponsibility
{

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function execute(callable $next)
    {
        $this->config->initSettings();
        $this->config->parseDir(path('app'));

        return $next();
    }

}
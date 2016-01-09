<?php

namespace Pckg\Framework\Config\Command;

use Pckg\Concept\AbstractChainOfReponsibility;

use Pckg\Framework\Config;

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
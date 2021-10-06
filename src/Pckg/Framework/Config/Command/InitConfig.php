<?php

namespace Pckg\Framework\Config\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Framework\Config;

class InitConfig extends AbstractChainOfReponsibility
{

    /**
     * @var Config
     */
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function execute(callable $next)
    {
        $this->config->parseDir(path('app'));

        trigger(InitConfig::class . '.executed');

        return $next();
    }
}

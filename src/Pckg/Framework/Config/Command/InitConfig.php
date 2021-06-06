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
        $path = null;
        try {
            $path = path('app');
        } catch (\Throwable $e) {
            return $next();
        }

        $this->config->parseDir($path);

        return $next();
    }
}

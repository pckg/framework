<?php

namespace Pckg\Framework\Config\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
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

        \Locale::setDefault(config('pckg.locale.default', 'en_US'));
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            \Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        }

        return $next();
    }

}
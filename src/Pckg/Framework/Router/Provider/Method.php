<?php

namespace Pckg\Framework\Router\Provider;

use Pckg\Concept\Reflect;
use Pckg\Framework\Router\RouteProviderInterface;

class Method implements RouteProviderInterface
{

    protected $namespace;

    protected $config;

    public function __construct($method, $config)
    {
        $this->method = $method;
        $this->config = $config;
    }

    public function init()
    {
        list($namespace, $method) = explode('::', $this->config);
        Reflect::method($namespace, $method);
    }

    public function getMatch()
    {
    }
}

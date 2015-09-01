<?php

namespace Pckg\Framework\Router\Provider;

use Pckg\Framework\Helper\Reflect;
use Pckg\Framework\Router\RouteProviderInterface;

/*
$namespaceProvider = new (new NamespaceProvider('Weblab\Generic')->setPrefix('/weblab-generic');
$router->addProvider($vendorProvider);
Looks for speciffic package and its route configuration
*/

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
<?php

namespace Pckg\Router\Provider;

use Pckg\Reflect;
use Pckg\Router\RouteProviderInterface;

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
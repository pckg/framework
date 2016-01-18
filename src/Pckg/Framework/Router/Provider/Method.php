<?php

namespace Pckg\Framework\Router\Provider;

use Pckg\Concept\Reflect;
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
        startMeasure('Method RouterProvider: ' . $namespace . ' ' . $method);
        Reflect::method($namespace, $method);
        stopMeasure('Method RouterProvider: ' . $namespace . ' ' . $method);
    }

    public function getMatch()
    {

    }

}
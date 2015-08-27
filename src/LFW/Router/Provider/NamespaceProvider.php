<?php

namespace Pckg\Router\Provider;

use Pckg\Router\RouteProviderInterface;
use Pckg\Router\Url;
use Pckg\Router\Yml;

/*
$namespaceProvider = new (new NamespaceProvider('Weblab\Generic')->setPrefix('/weblab-generic');
$router->addProvider($vendorProvider);
Looks for speciffic package and its route configuration
*/

class NamespaceProvider implements RouteProviderInterface
{

    protected $namespace;
    protected $config;

    public function __construct($namespace, $config)
    {
        $this->namespace = $namespace;
        $this->config = $config;
    }

    public function init()
    {
        $explNamespace = explode('\\', $this->namespace);

        $arrMethods = get_class_methods($this->namespace . '\Controller\\' . end($explNamespace));

        foreach ($arrMethods AS $method) {
            if (substr($method, -6) == 'Action') {
                $action = substr($method, 0, -6);
                $urlProvider = new Url((isset($this->config['prefix']) ? $this->config['prefix'] : null) . '/' . $action, [
                    'controller' => $this->namespace,
                    'view' => $action,
                ]);
                $urlProvider->load();
                router()->addProvider($urlProvider);
            }
        }

        $configPath = $this->config['src'] . str_replace('\\', '/', $this->namespace);
        $configProvider = new Yml(['src' => $configPath, 'prefix' => isset($this->config['prefix']) ? $this->config['prefix'] : null]);
        $configProvider->load();
        router()->addProvider($configProvider);
    }

    public function getMatch()
    {

    }

}
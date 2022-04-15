<?php

namespace Pckg\Framework\Router\Provider;

use Pckg\Framework\Router\RouteProviderInterface;
use Pckg\Framework\Router\URL;

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
        //startMeasure('Namespace RouterProvider: ' . $this->namespace);
        $explNamespace = explode('\\', $this->namespace);

        $arrMethods = get_class_methods($this->namespace . '\Controller\\' . end($explNamespace));

        foreach ($arrMethods as $method) {
            if (substr($method, -6) == 'Action') {
                $action = substr($method, 0, -6);
                /**
                 * @phpstan-ignore-next-line
                 */
                $urlProvider = new URL(
                    (isset($this->config['prefix']) ? $this->config['prefix'] : null) . '/' . $action,
                    [
                        'controller' => $this->namespace,
                        'view'       => $action,
                    ]
                );
                $urlProvider->load();
            }
        }

        $configPath = $this->config['src'] . str_replace('\\', '/', $this->namespace);
        $phpProvider = new Php(
            [
                'src'    => $configPath,
                'prefix' => isset($this->config['prefix']) ? $this->config['prefix'] : null,
            ]
        );
        $phpProvider->load();
        //stopMeasure('Namespace RouterProvider: ' . $this->namespace);
    }

    public function getMatch()
    {
    }
}

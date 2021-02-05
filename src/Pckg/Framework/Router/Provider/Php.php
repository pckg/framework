<?php

namespace Pckg\Framework\Router\Provider;

use Pckg\Concept\Reflect;
use Pckg\Framework\Router\RouteProviderInterface;

class Php implements RouteProviderInterface
{

    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function init()
    {
        //startMeasure('Php RouterProvider: ' . $this->config['file']);
        $router = require $this->config['file'];

        $prefix = isset($this->config['prefix'])
            ? $this->config['prefix']
            : null;

        if (isset($router['providers'])) {
            foreach ($router['providers'] as $providerType => $arrProviders) {
                foreach ($arrProviders as $provider => $providerConfig) {
                    if (isset($providerConfig['prefix'])) {
                        $providerConfig['prefix'] = $prefix . (isset($providerConfig['prefix'])
                                ? $providerConfig['prefix']
                                : '');
                    }
                    $routeProvider = Reflect::create(
                        'Pckg\\Framework\\Router\\Provider\\' . ucfirst($providerType),
                        [
                            $providerType => $prefix . $provider,
                            'config'      => $providerConfig,
                        ]
                    );
                    $routeProvider->init();
                }
            }
        }
        //stopMeasure('Php RouterProvider: ' . $this->config['file']);
    }

    public function getMatch()
    {
        // TODO: Implement getMatch() method.
    }
}

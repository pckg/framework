<?php

namespace Pckg\Router\Provider;

use Pckg\Reflect;
use Pckg\Router\RouteProviderInterface;

class Yml implements RouteProviderInterface
{

    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function init()
    {
        $yaml = new \Symfony\Component\Yaml\Yaml();

        $router = $yaml->parse(file_get_contents($this->config['file']));

        $prefix = isset($this->config['prefix'])
            ? $this->config['prefix']
            : null;

        if (isset($router['providers'])) {

            foreach ($router['providers'] AS $providerType => $arrProviders) {
                foreach ($arrProviders AS $provider => $providerConfig) {
                    if (isset($providerConfig['prefix'])) {
                        $providerConfig['prefix'] = $prefix . (isset($providerConfig['prefix'])
                                ? $providerConfig['prefix']
                                : '');
                    }
                    $routeProvider = Reflect::create('LFW\Router\\Provider\\' . ucfirst($providerType), [
                        $providerType => $prefix . $provider,
                        'config' => $providerConfig,
                    ]);
                    $routeProvider->init();
                    router()->addProvider($routeProvider);
                }
            }
        }
    }

    public function getMatch()
    {
        // TODO: Implement getMatch() method.
    }


}
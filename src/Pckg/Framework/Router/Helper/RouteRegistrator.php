<?php

namespace Pckg\Framework\Router\Helper;

use Pckg\Framework\Helper\Reflect;

trait RouteRegistrator
{

    /**
     * @param $routes
     *
     * @throws \Exception
     */
    public function registerRoutes($routes)
    {
        foreach ($routes AS $providerType => $arrProviders) {
            foreach ($arrProviders AS $provider => $providerConfig) {
                if (isset($providerConfig['prefix'])) {
                    $providerConfig['prefix'] = '';
                }

                $routeProvider = Reflect::create('Pckg\\Framework\\Router\\Provider\\' . ucfirst($providerType), [
                    $providerType => $provider,
                    'config'      => $providerConfig,
                ]);
                $routeProvider->init();
            }
        }
    }

}
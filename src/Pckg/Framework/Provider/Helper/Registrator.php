<?php

namespace Pckg\Framework\Provider\Helper;

use Pckg\Concept\Reflect;
use Pckg\Framework\Provider;
use Pckg\Framework\Provider\ProviderManager;
use Pckg\Framework\View\Twig;

trait Registrator
{

    public function registerAutoloaders($autoloaders, $object = null)
    {
        foreach ($autoloaders as $autoloader) {
            autoloader()->add('', $autoloader);
            Twig::addDir($autoloader);
        }

        if ($object && method_exists($object, 'autoloadApps')) {
            $this->registerApps($object->autoloadApps());
        }
    }

    public function registerProviders($providers, ProviderManager $manager)
    {
        foreach ($providers as $provider => $config) {
            if (is_int($provider)) {
                $provider = $config;
            }

            $provider = new $provider($manager);
            $provider->register();
        }
    }

    public function registerApps($apps)
    {
        foreach ($apps as $app) {
            $appDir = path('apps') . strtolower($app) . path('ds') . 'src';
            $this->registerAutoloaders([$appDir]);

            $appObject = Reflect::create($app);

            $this->registerAutoloaders($appObject->autoload(), $appObject);
            $this->registerProviders($appObject->providers(), $appObject);
        }
    }

}
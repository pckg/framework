<?php

namespace Pckg\Framework\Provider\Helper;

use Pckg\Concept\Reflect;
use Pckg\Framework\Provider;
use Pckg\Framework\View\Twig;

trait Registrator
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

                Reflect::create('Pckg\\Framework\\Router\\Provider\\' . ucfirst($providerType), [
                    $providerType => $provider,
                    'config'      => $providerConfig,
                ])->init();
            }
        }
    }

    public function registerAutoloaders($autoloaders, $object = null)
    {
        if (!is_array($autoloaders)) {
            $autoloaders = [$autoloaders];
        }

        foreach ($autoloaders as $autoloader) {
            autoloader()->add('', $autoloader);
            Twig::addDir($autoloader);
        }

        /**
         * @T00D00 - this needs to be implemented in provider
         */
        if ($object && method_exists($object, 'autoloadApps')) {
            $this->registerApps($object->autoloadApps());
        }
    }

    public function registerProviders($providers)
    {
        if (!is_array($providers)) {
            $providers = [$providers];
        }

        foreach ($providers as $provider => $config) {
            if (is_int($provider)) {
                $provider = $config;
            }

            Reflect::create($provider)->register();
        }
    }

    public function registerApps($apps)
    {
        if (!is_array($apps)) {
            $apps = [$apps];
        }

        foreach ($apps as $app) {
            $appDir = path('apps') . strtolower($app) . path('ds') . 'src';
            $this->registerAutoloaders($appDir);

            $appObject = Reflect::create($app);

            $this->registerAutoloaders($appObject->autoload(), $appObject);
            $this->registerProviders($appObject->providers(), $appObject);
        }
    }

    /**
     * Double: provider
     * @param $consoles
     */
    public function registerConsoles($consoles)
    {
        if (!context()->exists('ConsoleApplication')) {
            return;
        }

        $consoleApplication = context()->get('ConsoleApplication');
        foreach ($consoles as $console) {
            $consoleApplication->add(new $console);
        }
    }

    public function registerAssets($assets)
    {
        foreach ($assets as $key => $assets) {
            $this->assetManager->addProviderAssets($assets, is_array($assets) ? $key : 'main', $this);
        }
    }

    public function registerPaths($paths)
    {
        // @T00D00
    }

    public function registerMiddlewares($middlewares)
    {
        // @T00D00
    }

}
<?php

namespace Pckg\Framework\Provider\Helper;

use Composer\Autoload\ClassLoader;
use Pckg\Concept\Event\Dispatcher;
use Pckg\Concept\Reflect;
use Pckg\Framework\Response;
use Pckg\Framework\Stack;
use Pckg\Framework\View\Twig;
use Pckg\Manager\Asset;
use Pckg\Manager\Job;
use Symfony\Component\Console\Application as SymfonyConsole;

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
            if (is_object($arrProviders)) {
                /**
                 * $arrProviders is instance of Group or Route.
                 */
                $arrProviders->register([
                                            'provider' => get_class($this),
                                        ]);
                continue;
            }

            foreach ($arrProviders AS $provider => $providerConfig) {
                if (isset($providerConfig['prefix'])) {
                    $providerConfig['prefix'] = '';
                }
                if (is_array($providerConfig)) {
                    $providerConfig['provider'] = get_class($this);
                }

                Reflect::create(
                    'Pckg\\Framework\\Router\\Provider\\' . ucfirst($providerType),
                    [
                        $providerType => $provider,
                        'config'      => $providerConfig,
                    ]
                )->init();
            }
        }
    }

    public function registerAutoloaders($autoloaders, $object = null)
    {
        if (!is_array($autoloaders)) {
            $autoloaders = [$autoloaders];
        }

        foreach ($autoloaders as $autoloader) {
            if (!is_array($autoloader)) {
                $autoloader = [$autoloader];
            }
            foreach ($autoloader as $a) {
                autoloader()->add('', $a, $a == path('app_src'));
                Twig::addDir($a);
            }
        }
    }

    public function registerClassMaps($classMap)
    {
        if (!$classMap) {
            return;
        }

        if (!is_array($classMap)) {
            $classMap = [$classMap];
        }

        $loader = new ClassLoader();
        $loader->addClassMap($classMap);
        $loader->register(true);
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
        /**
         * Apps need to be initialized in reverse direction.
         * Now, how will we manage to do this?
         *
         */
        if (!is_array($apps)) {
            $apps = [$apps];
        }

        $stack = context()->get(Stack::class);
        foreach ($apps as $app) {
            $appDir = path('apps') . strtolower($app) . path('ds') . 'src';
            $this->registerAutoloaders($appDir);

            $appObject = Reflect::create(ucfirst($app));
            $appObject->register();

            $stack->push(
                function() use ($app) {
                    config()->parseDir(path('apps') . strtolower($app) . path('ds'));
                }
            );
        }
    }

    /**
     * Double: provider
     *
     * @param $consoles
     */
    public function registerConsoles($consoles)
    {
        if (!context()->exists(SymfonyConsole::class)) {
            return;
        }

        $consoleApplication = context()->get(SymfonyConsole::class);
        foreach ($consoles as $console) {
            $consoleApplication->add(new $console);
        }
    }

    public function registerAssets($assets)
    {
        if (!$assets) {
            return;
        }

        $assetManager = context()->getOrCreate(Asset::class);
        foreach ($assets as $key => $assets) {
            $assetManager->addProviderAssets($assets, is_array($assets) ? $key : 'main', $this);
        }
    }

    public function registerPaths($paths)
    {
        $this->registerAutoloaders($paths);
    }

    public function registerMiddlewares($middlewares)
    {
        $response = context()->getOrCreate(Response::class);
        foreach ($middlewares as $middleware) {
            $response->addMiddleware($middleware);
        }
    }

    public function registerAfterwares($afterwares)
    {
        $response = context()->getOrCreate(Response::class);
        foreach ($afterwares as $afterware) {
            $response->addAfterware($afterware);
        }
    }

    public function registerViewObjects($objects)
    {
        foreach ($objects as $key => $val) {
            if ($val && is_string($val)) {
                $val = context()->getOrCreate($val);
            }
            Twig::setStaticData($key, $val);
        }
    }

    public function registerListeners($handlers)
    {
        $dispatcher = context()->getOrCreate(Dispatcher::class);
        foreach ($handlers as $event => $listeners) {
            if (!is_array($listeners)) {
                $listeners = [$listeners];
            }

            foreach ($listeners as $listener) {
                $dispatcher->listen($event, $listener);
            }
        }
    }

    public function registerJobs($jobs)
    {
        $jobManager = context()->getOrCreate(Job::class);

        foreach ($jobs as $job) {
            $jobManager->add($job);
        }
    }

}
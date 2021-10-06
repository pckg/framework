<?php

namespace Pckg\Framework\Provider\Helper;

use Composer\Autoload\ClassLoader;
use Pckg\Concept\Event\Dispatcher;
use Pckg\Concept\Reflect;
use Pckg\Framework\Provider;
use Pckg\Framework\Response;
use Pckg\Framework\Stack;
use Pckg\Framework\View\Twig;
use Pckg\Manager\Asset;
use Pckg\Manager\Job;
use Pckg\Translator\Service\Translator;
use ReflectionClass;
use Symfony\Component\Console\Application as SymfonyConsole;

trait Registrator
{

    protected $routePrefix = null;

    public function setRoutePrefix($prefix)
    {
        $this->routePrefix = $prefix;

        return $this;
    }

    public function getTranslationPath()
    {
        $class = static::class;
        $reflector = new ReflectionClass($class);
        $file = $reflector->getFileName();

        return realpath(substr($file, 0, strrpos($file, path('ds'))) . path('ds') . '..' . path('ds') . 'lang');
    }

    /**
     * @param $routes
     *
     * @throws \Exception
     */
    public function registerRoutes($routes)
    {
        foreach ($routes as $providerType => $arrProviders) {
            if (is_object($arrProviders)) {
                /**
                 * $arrProviders is instance of Group or Route.
                 */
                $arrProviders->register(
                    [
                        'provider' => get_class($this),
                        'urlPrefix' => $this->routePrefix,
                    ]
                );
                continue;
            }

            foreach ($arrProviders as $provider => $providerConfig) {
                if (isset($providerConfig['prefix'])) {
                    // why reset?
                    $providerConfig['prefix'] = '';
                }
                if (is_array($providerConfig)) {
                    $providerConfig['provider'] = get_class($this);
                }

                Reflect::create(
                    'Pckg\\Framework\\Router\\Provider\\' . ucfirst($providerType),
                    [
                        $providerType => is_string($provider) ? $this->routePrefix . $provider : $provider,
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

            if (is_string($provider)) {
                $provider = context()->getOrCreate($provider);
            }

            try {
                $provider->register();
            } catch (\Throwable $e) {
                throw new \Exception('Error registering provider ' . get_class($provider) . ':' . exception($e), null);
            }
        }
    }

    public function registerApps($apps)
    {
        /**
         * Apps need to be initialized in reverse direction.
         * Now, how will we manage to do this?
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
                function () use ($app) {
                    config()->parseDir(path('apps') . strtolower($app) . path('ds'));
                }
            );
        }

        if ($stack->getStacks() && !config('app_parent')) {
            config()->set('app_parent', $stack->getStacks() ? $apps[0] : config('app', null));
        }

        // reparse config
        if ($apps) {
            $stack->push(fn() => config()->parseDir(path('app')));
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
            $consoleApplication->add(new $console());
        }
    }

    public function registerAssets($assets)
    {
        if (!$assets) {
            return;
        }

        $assetManager = context()->getOrCreate(Asset::class);
        foreach ($assets as $key => $a) {
            $assetManager->addProviderAssets($a, is_string($key) ? $key : 'main', $this);
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

    public function registerTranslations()
    {
        if (!isset($this->translations)) {
            return;
        }

        $translatorService = context()->getOrCreate(Translator::class);
        $translatorService->addDir($this->getTranslationPath());
    }

    public function registerServices($services)
    {
        foreach ($services as $service => $initiator) {
            context()->whenRequested($service, $initiator);
        }
    }
}

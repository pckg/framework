<?php

namespace Pckg\Framework;

use Pckg\Concept\Reflect;
use Pckg\Framework\Provider\Helper\Registrator;
use ReflectionClass;

class Provider
{

    use Registrator;

    protected $registered = false;

    protected $translations = false;

    protected $routePrefix = null;

    public function shouldRegister()
    {
        return !$this->registered;
    }

    /**
     * Register options
     */
    public function register()
    {
        if (!$this->shouldRegister()) {
            return $this;
        }

        $hadStack = context()->exists(Stack::class);
        if (!$hadStack) {
            context()->bind(Stack::class, new Stack());
        }

        $this->registerAutoloaders($this->autoload());
        $this->registerClassMaps($this->classMaps());
        $this->registerApps($this->apps());
        $this->registerProviders($this->providers());
        $this->registerRoutes($this->routes());
        $this->registerListeners($this->listeners());
        $this->registerMiddlewares($this->middlewares());
        $this->registerAfterwares($this->afterwares());
        $this->registerPaths($this->paths());
        $this->registerViewObjects($this->viewObjects());
        $this->registerConsoles($this->consoles());
        $this->registerAssets($this->assets());
        $this->registerJobs($this->jobs());
        $this->registerTranslations();

        if (method_exists($this, 'registered')) {
            Reflect::method($this, 'registered');
        }

        $this->registered = true;

        /**
         * Some actions needs to be executed in reverse direction, for example config initialization.
         */
        if (!$hadStack) {
            $stack = context()->get(Stack::class);
            $stack->execute();
        }

        return $this;
    }

    public function setRoutePrefix($prefix)
    {
        $this->routePrefix = $prefix;

        return $this;
    }

    protected function getViewPaths()
    {
        $db = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        $file = $db[0]['file'];
        $class = $db[1]['class'];

        $paths = [
            realpath(substr($file, 0, strrpos($file, path('ds'))) . path('ds') . '..' . path('ds') . 'View'),
            substr($file, 0, -strlen($class) - strlen('.php')),
        ];

        foreach ($paths as $i => $path) {
            if (!$path || !is_dir($path)) {
                unset($paths[$i]);
            }
        }

        return $paths;
    }

    public function getTranslationPath()
    {
        $class = static::class;
        $reflector = new ReflectionClass($class);
        $file = $reflector->getFileName();

        return realpath(substr($file, 0, strrpos($file, path('ds'))) . path('ds') . '..' . path('ds') . 'lang');
    }

    public function apps()
    {
        return [];
    }

    public function providers()
    {
        return [];
    }

    public function routes()
    {
        return [];
    }

    public function middlewares()
    {
        return [];
    }

    public function afterwares()
    {
        return [];
    }

    public function paths()
    {
        return [];
    }

    public function consoles()
    {
        return [];
    }

    public function assets()
    {
        return [];
    }

    public function viewObjects()
    {
        return [];
    }

    public function listeners()
    {
        return [];
    }

    public function autoload()
    {
        return [];
    }

    public function classMaps()
    {
        return [];
    }

    public function jobs()
    {
        return [];
    }

}
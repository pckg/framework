<?php

namespace Pckg\Framework;

use Pckg\Concept\Context;
use Pckg\Concept\Reflect;
use Pckg\Framework\Environment\Command\DefinePaths;
use Pckg\Framework\Environment\Development;
use Pckg\Framework\Environment\Production;

abstract class Environment
{
    protected $urlPrefix = '/index.php';

    protected $env;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Config
     */
    protected $config;

    const EVENT_INITIALIZING = self::class . '.initializing';

    const EVENT_INITIALIZED = self::class . '.initialized';

    public function __construct(Config $config, Context $context)
    {
        $this->config = $config;
        $this->context = $context;

        $this->context->bind(Config::class, $this->config);
    }

    public function getUrlPrefix()
    {
        return $this->urlPrefix;
    }

    /**
     * @param $url
     * @return false|mixed|string
     * @deprecated
     */
    public function replaceUrlPrefix($url)
    {
        if (strpos($url, $this->urlPrefix . '/') === 0) {
            $url = substr($url, strlen($this->urlPrefix));
        }

        return $url;
    }

    public function init()
    {
        trigger(static::EVENT_INITIALIZING, [$this]);

        chain($this->initArray());

        trigger(static::EVENT_INITIALIZED, [$this]);

        return $this;
    }

    public function initArray()
    {
        return [
            DefinePaths::class,
        ];
    }

    public function isDev()
    {
        return static::class == Development::class;
    }

    public function isPro()
    {
        return static::class == Production::class;
    }

    public function isLocal()
    {
        return config('local', false) === true;
    }

    public function registerExceptionHandler()
    {
    }

    public function isWin()
    {
    }

    public function isUnix()
    {
    }

    /**
     * @param Helper\Context $context
     * @param                $appName
     * @return Application
     */
    abstract public function createApplication(\Pckg\Framework\Helper\Context $context, $appName);

    public function registerAndBindApplication(\Pckg\Framework\Helper\Context $context, $appName)
    {
        /**
         * Register active paths.
         */
        $this->registerAppPaths($appName);

        /**
         * Add app src dir to autoloader and template engine.
         */
        $context->registerAutoloaders(path('app_src'), $this);

        /**
         * Now we will be able to create and register application provider.
         */
        return $applicationProvider = Reflect::create(ucfirst($appName));
    }

    public function registerAppPaths($appName)
    {
        path('app', path('root') . "app" . path('ds') . strtolower($appName) . path('ds'));
        path('app_src', path('app') . "src" . path('ds'));
    }
}

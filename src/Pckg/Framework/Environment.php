<?php

namespace Pckg\Framework;

use Pckg\Framework\Environment\Command\DefinePaths;
use Pckg\Framework\Environment\Development;
use Pckg\Framework\Environment\Production;
use Pckg\Manager\Asset\AssetManager;

class Environment implements AssetManager
{

    protected $urlPrefix = '/index.php';

    protected $env;

    public function getUrlPrefix()
    {
        return $this->urlPrefix;
    }

    public function replaceUrlPrefix($url)
    {
        if (strpos($url, $this->urlPrefix . '/') === 0) {
            $url = substr($url, strlen($this->urlPrefix));
        }

        return $url;
    }

    public function init()
    {
        trigger(Environment::class . '.initializing', [$this]);

        chain($this->initArray());

        trigger(Environment::class . '.initialized', [$this]);

        return $this;
    }

    public function initArray()
    {
        return [
            DefinePaths::class,
        ];
    }

    function isDev()
    {
        return static::class == Development::class;
    }

    function isPro()
    {
        return static::class == Production::class;
    }

    function isLocal()
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

    public function assets()
    {
        return [];
    }

}
<?php

namespace Pckg\Framework;

use Pckg\Concept\Initializable;
use Pckg\Framework\Environment\Command\DefinePaths;
use Pckg\Framework\Environment\Development;
use Pckg\Framework\Environment\Production;

class Environment
{

    protected $urlPrefix = '/index.php';

    protected $env;

    public function getUrlPrefix()
    {
        return $this->urlPrefix;
    }

    public function init()
    {
        chain([
            DefinePaths::class,
        ]);

        return $this;
    }

    function isDev()
    {
        return static::class == Development::class;
    }

    function isPro()
    {
        return static::class == Production::class;
    }

    public function registerExceptionHandler() {
    }

    public function isWin() {

    }

    public function isUnix() {

    }

}
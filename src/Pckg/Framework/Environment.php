<?php

namespace Pckg\Framework;

use Pckg\Concept\Initializable;
use Pckg\Framework\Environment\Development;
use Pckg\Framework\Environment\Production;

class Environment
{

    use Initializable;

    protected $urlPrefix = '/index.php';

    protected $env;

    public function getUrlPrefix()
    {
        return $this->urlPrefix;
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

}
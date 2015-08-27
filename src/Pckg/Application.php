<?php

namespace Pckg;

use Pckg\Application\ApplicationInterface;
use Pckg\Concept\Initializable;
use Pckg\Concept\Middleware;
use Pckg\Concept\Runnable;

class Application implements ApplicationInterface
{

    use Initializable, Runnable, Middleware;

    protected $name;

    protected $mapper = [];

    public function getName()
    {
        return $this->name;
    }

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getMapped($key, $second)
    {
        return isset($this->mapper[$key][$second])
            ? $this->mapper[$key][$second]
            : null;
    }

}
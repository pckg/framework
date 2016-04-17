<?php

namespace Pckg\Framework\Router\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Framework\Application;
use Pckg\Framework\Provider\Helper\Registrator;

class RegisterRoutes extends AbstractChainOfReponsibility
{

    use Registrator;

    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function execute(callable $next)
    {
        $this->registerRoutes($this->application->routes());

        return $next();
    }

}
<?php

namespace Pckg\Framework\Router\Command;

use Pckg\Concept\AbstractChainOfReponsibility;

use Pckg\Concept\Context;
use Pckg\Framework\Application;
use Pckg\Framework\Application\Website;
use Pckg\Framework\Router;

class InitRouter extends AbstractChainOfReponsibility
{

    protected $router;

    protected $context;

    protected $application;

    public function __construct(Router $router, Context $context, Application $application)
    {
        $this->router = $router;
        $this->context = $context;
        $this->application = $application;
    }

    public function execute(callable $next)
    {
        $this->context->bind("Router", $this->router);

        $this->router->init();

        return $next();
    }

}
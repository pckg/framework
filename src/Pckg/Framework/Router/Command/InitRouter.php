<?php

namespace Pckg\Framework\Router\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Concept\Context;
use Pckg\Concept\Reflect;
use Pckg\Framework\Application;
use Pckg\Framework\Response;
use Pckg\Framework\Router;

class InitRouter extends AbstractChainOfReponsibility
{

    protected $router;

    protected $context;

    protected $application;

    public function __construct(Router $router, Context $context)
    {
        $this->router = $router;
        $this->context = $context;
    }

    public function execute(callable $next)
    {
        $this->context->bind(Router::class, $this->router);

        $this->router->init();

        return $next();
    }
}

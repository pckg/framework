<?php

namespace Pckg\Framework\Router\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Concept\Context;
use Pckg\Concept\Reflect;
use Pckg\Database\Record\Resolver as RecordResolver;
use Pckg\Framework\Application;
use Pckg\Framework\Response;
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

        Reflect::prependResolver(Reflect::create(RecordResolver::class, [$this]));

        return $next();
    }

}
<?php

namespace Pckg\Router\Command;

use Pckg\Concept\AbstractChainOfReponsibility;

use Pckg\Context;
use Pckg\Router;

class InitRouter extends AbstractChainOfReponsibility
{

    protected $router;

    protected $context;

    public function __construct(Router $router, Context $context)
    {
        $this->router = $router;
        $this->context = $context;
    }

    public function execute()
    {
        $this->context->bind("Router", $this->router);

        $this->router->init();

        $this->next->execute();
    }

}
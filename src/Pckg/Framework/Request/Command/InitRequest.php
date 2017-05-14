<?php

namespace Pckg\Framework\Request\Command;

use Exception;
use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Concept\Context;
use Pckg\Framework\Request;
use Pckg\Framework\Router;
use Pckg\Framework\Router\Command\ResolveRoute;

class InitRequest extends AbstractChainOfReponsibility
{

    protected $request;

    protected $context;

    protected $router;

    public function __construct(Request $request, Context $context, Router $router)
    {
        $this->request = $request;
        $this->context = $context;
        $this->router = $router;
    }

    public function execute(callable $next)
    {
        $this->context->bind(Request::class, $this->request);

        trigger(Request::class . '.initializing', [$this->request]);

        $url = $this->request->getUrl();

        $match = (new ResolveRoute($this->router, $url))->execute();

        if (!$match) {
            throw new Exception("Cannot find route's match (1): " . $url);
        }

        $this->request->setMatch($match);

        trigger(Request::class . '.initialized', [$this->request]);

        return $next();
    }

}
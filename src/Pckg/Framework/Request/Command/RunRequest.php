<?php

namespace Pckg\Framework\Request\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Concept\Reflect;
use Pckg\Framework\Request;
use Pckg\Framework\Response\Command\ProcessRouteMatch;

class RunRequest extends AbstractChainOfReponsibility
{

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function execute(callable $next)
    {
        trigger(Request::class . '.running', [$this->request]);

        Reflect::create(ProcessRouteMatch::class)->execute();

        trigger(Request::class . '.ran', [$this->request]);

        return $next();
    }
}

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
        trigger('request.running', [$this->request]);

        Reflect::create(ProcessRouteMatch::class, ['match' => $this->request->getMatch()])->execute();

        trigger('request.ran', [$this->request]);

        return $next();
    }

}
<?php

namespace Pckg\Framework\Request\Command;

use Pckg\Concept\AbstractChainOfReponsibility;

use Pckg\Framework\Request;

class RunRequest extends AbstractChainOfReponsibility
{

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function execute(callable $next)
    {
        $this->request->run();

        return $next();
    }

}
<?php

namespace Pckg\Framework\Request\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Concept\Context;
use Pckg\Framework\Request;

class InitRequest extends AbstractChainOfReponsibility
{
    protected $request;

    protected $context;

    public function __construct(Request $request, Context $context)
    {
        $this->request = $request;
        $this->context = $context;
    }

    public function execute(callable $next)
    {
        $this->context->bind(Request::class, $this->request);

        return $next();
    }
}

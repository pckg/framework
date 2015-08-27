<?php

namespace Pckg\Request\Command;

use Pckg\Concept\AbstractChainOfReponsibility;

use Pckg\Context;
use Pckg\Request;

class InitRequest extends AbstractChainOfReponsibility
{

    protected $request;

    protected $context;

    public function __construct(Request $request, Context $context)
    {
        $this->request = $request;
        $this->context = $context;
    }

    public function execute()
    {
        $this->context->bind('Request', $this->request);

        $this->request->init();

        $this->next->execute();
    }

}
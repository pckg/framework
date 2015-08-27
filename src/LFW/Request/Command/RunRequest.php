<?php

namespace Pckg\Request\Command;

use Pckg\Concept\AbstractChainOfReponsibility;

use Pckg\Request;

class RunRequest extends AbstractChainOfReponsibility
{

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        $this->request->run();

        $this->next->execute();

        /*
         * 1. Run request - find routing match
         * 2. Run after request - add assets
         * 3. Run response - echo response
         * */
    }

}
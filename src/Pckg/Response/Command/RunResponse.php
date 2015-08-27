<?php

namespace Pckg\Response\Command;

use Pckg\Concept\AbstractChainOfReponsibility;

use Pckg\Response;

class RunResponse extends AbstractChainOfReponsibility
{

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function execute()
    {
        $this->response->run();

        $this->next->execute();
    }

}
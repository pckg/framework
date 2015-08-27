<?php

namespace Pckg\Framework\Response\Command;

use Pckg\Concept\AbstractChainOfReponsibility;

use Pckg\Framework\Response;

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
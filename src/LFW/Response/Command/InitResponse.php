<?php


namespace Pckg\Response\Command;

use Pckg\Concept\AbstractChainOfReponsibility;

use Pckg\Context;
use Pckg\Response;

class InitResponse extends AbstractChainOfReponsibility
{

    protected $response;

    protected $context;

    public function __construct(Response $response, Context $context)
    {
        $this->response = $response;
        $this->context = $context;
    }

    public function execute()
    {
        $this->context->bind('Response', $this->response);

        $this->response->init();

        $this->next->execute();
    }

}
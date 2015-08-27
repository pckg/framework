<?php

namespace Pckg\Framework\Request\Session\Command;

use Pckg\Concept\AbstractChainOfReponsibility;

use Pckg\Context;
use Pckg\Framework\Request\Data\Session;

class InitSession extends AbstractChainOfReponsibility
{

    protected $session;

    protected $context;

    public function __construct(Session $session, Context $context)
    {
        $this->session = $session;
        $this->context = $context;
    }

    public function execute()
    {
        $this->context->bind('Session', $this->session);

        $this->session->init();

        $this->next->execute();
    }

}
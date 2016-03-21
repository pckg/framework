<?php

namespace Pckg\Framework\Request\Session\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Concept\Context;
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

    public function execute(callable $next)
    {
        $this->context->bind('Session', $this->session);

        $this->session->init();

        return $next();
    }

}
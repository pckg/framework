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
        $this->context->bind(Session::class, $this->session);

        $SID = session_id();
        if (empty($SID)) {
            /**
             * Keep session data in server in client for 1h by default.
             */
            $time = 7 * 24 * 60 * 60;
            ini_set('session.gc_maxlifetime', $time);
            session_set_cookie_params($time);

            session_start(/*[
                              'cookie_lifetime' => $time,
                          ]*/);
        }

        //$this->session->setPointerData($_SESSION);

        return $next();
    }

}
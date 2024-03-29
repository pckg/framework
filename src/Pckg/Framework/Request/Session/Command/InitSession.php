<?php

namespace Pckg\Framework\Request\Session\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Framework\Request\Data\Flash;
use Pckg\Framework\Request\Data\Session;

class InitSession extends AbstractChainOfReponsibility
{
    public function execute(callable $next)
    {
        if (!config('pckg.session.disabled')) {
            context()->getOrCreate(Session::class);
            context()->getOrCreate(Flash::class);
        }

        return $next();
    }
}

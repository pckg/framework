<?php

namespace Pckg\Framework\Application\Console\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Framework\Application\Console;

class RegisterCommands extends AbstractChainOfReponsibility
{

    protected $console;

    public function __construct(Console $console)
    {
        $this->console = $console;
    }

    public function execute(callable $next)
    {
        // is this needed?x

        return $next();
    }

}
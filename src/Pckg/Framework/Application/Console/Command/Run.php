<?php

namespace Pckg\Framework\Application\Console\Command;

use Pckg\Framework\Application;

class Run
{

    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function execute(callable $next)
    {
        chain([
            RunCommand::class,
        ], 'execute', [$this->application]);

        return $next();
    }

}
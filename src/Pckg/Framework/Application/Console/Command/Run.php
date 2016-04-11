<?php

namespace Pckg\Framework\Application\Console\Command;

use Pckg\Framework\Application\ApplicationInterface;

class Run
{

    protected $application;

    public function __construct(ApplicationInterface $application)
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
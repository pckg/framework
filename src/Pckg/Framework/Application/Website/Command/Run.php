<?php

namespace Pckg\Framework\Application\Website\Command;

use Pckg\Framework\Application;
use Pckg\Framework\Request\Command\RunRequest;
use Pckg\Framework\Response\Command\RunResponse;

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
            RunRequest::class,
            RunResponse::class,
        ], 'execute', [$this->application]);

        return $next();
    }

}
<?php

namespace Pckg\Framework\Application\Website\Command;

use Pckg\Framework\Application\ApplicationInterface;
use Pckg\Framework\Request\Command\RunRequest;
use Pckg\Framework\Response\Command\RunResponse;

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
            RunRequest::class,
            RunResponse::class,
        ], 'execute', [$this->application]);

        return $next();
    }

}
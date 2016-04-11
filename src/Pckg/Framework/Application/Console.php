<?php namespace Pckg\Framework\Application;

use Pckg\Framework\Application;
use Pckg\Framework\Application\Console\Command\Init;
use Pckg\Framework\Application\Console\Command\Run;

class Console extends Application
{

    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function inits()
    {
        return [
            Init::class,
        ];
    }

    public function runs()
    {
        return [
            Run::class,
        ];
    }

}
<?php namespace Pckg\Framework\Application;

use Pckg\Framework\Application;
use Pckg\Framework\Application\Console\Command\Init;
use Pckg\Framework\Application\Console\Command\Run;

class Console extends Application
{

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
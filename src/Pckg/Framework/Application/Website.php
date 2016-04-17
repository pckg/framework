<?php

namespace Pckg\Framework\Application;

use Pckg\Framework\Application;
use Pckg\Framework\Application\Website\Command\Init;
use Pckg\Framework\Application\Website\Command\Run;

class Website extends Application
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
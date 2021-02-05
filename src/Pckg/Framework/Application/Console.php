<?php

namespace Pckg\Framework\Application;

use Pckg\Framework\Application;
use Pckg\Framework\Application\Command\InitDatabase;
use Pckg\Framework\Application\Command\RegisterApplication;
use Pckg\Framework\Application\Console\Command\RunCommand;
use Pckg\Framework\Config\Command\InitConfig;
use Pckg\Framework\Router\Command\InitRouter;
use Pckg\Locale\Command\Localize;

class Console extends Application
{

    public function inits()
    {
        return [
            InitConfig::class,
            Localize::class,
            InitDatabase::class,
            InitRouter::class,

            RegisterApplication::class,
        ];
    }

    public function runs()
    {
        return [
            RunCommand::class,
        ];
    }
}

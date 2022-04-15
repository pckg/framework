<?php

namespace Pckg\Framework\Application;

use Pckg\Framework\Application;
use Pckg\Framework\Application\Command\InitDatabase;
use Pckg\Framework\Application\Command\RegisterApplication;
use Pckg\Framework\Application\Console\Command\RunCommand;
use Pckg\Framework\Config\Command\InitConfig;
use Pckg\Framework\Router\Command\InitRouter;
use Pckg\Locale\Command\Localize;
use Pckg\Queue\Command\RunRabbitMQ;

class Queue extends Application
{
    public function inits()
    {
        return [
            InitConfig::class,
            Localize::class,
            RegisterApplication::class,
        ];
    }

    public function runs()
    {
        return [
            RunRabbitMQ::class,
        ];
    }
}

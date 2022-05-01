<?php

namespace Pckg\Framework\Application;

use Pckg\Framework\Application;
use Pckg\Framework\Application\Command\RegisterApplication;
use Pckg\Framework\Config\Command\InitConfig;
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

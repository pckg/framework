<?php namespace Pckg\Framework\Application;

use Pckg\Database\Command\InitDatabase;
use Pckg\Framework\Application;
use Pckg\Framework\Application\Command\RegisterApplication;
use Pckg\Framework\Application\Console\Command\RunCommand;
use Pckg\Framework\Config\Command\InitConfig;
use Pckg\Framework\Locale\Command\Localize;
use Pckg\Framework\Router\Command\InitRouter;

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
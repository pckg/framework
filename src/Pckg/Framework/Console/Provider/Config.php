<?php namespace Pckg\Framework\Console\Provider;

use Pckg\Framework\Console\Command\CreatePckgProject;
use Pckg\Framework\Provider;
use Pckg\Migration\Provider\Config as MigrationProvider;

class Config extends Provider
{

    public function consoles()
    {
        return [
            CreatePckgProject::class,
        ];
    }

    public function providers()
    {
        return [
            MigrationProvider::class,
        ];
    }

}
<?php namespace Pckg\Framework\Provider;

use Pckg\Framework\Console\CreatePckgProject;
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
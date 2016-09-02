<?php namespace Pckg\Framework\Console\Provider;

use Pckg\Framework\Console\Command\CreatePckgProject;
use Pckg\Framework\Console\Command\InstallProject;
use Pckg\Framework\Console\Command\TestProject;
use Pckg\Framework\Provider;
use Pckg\Migration\Provider\Migration as MigrationProvider;

class Console extends Provider
{

    public function consoles()
    {
        return [
            CreatePckgProject::class,
            InstallProject::class,
            TestProject::class,
        ];
    }

    public function providers()
    {
        return [
            MigrationProvider::class,
        ];
    }

}
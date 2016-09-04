<?php namespace Pckg\Framework\Console\Provider;

use Pckg\Framework\Console\Command\CreatePckgProject;
use Pckg\Framework\Console\Command\FeatureProject;
use Pckg\Framework\Console\Command\InitProject;
use Pckg\Framework\Console\Command\PreprodProject;
use Pckg\Framework\Console\Command\ProdProject;
use Pckg\Framework\Console\Command\PullProject;
use Pckg\Framework\Console\Command\TestProject;
use Pckg\Framework\Console\Command\UpdateProject;
use Pckg\Framework\Provider;
use Pckg\Migration\Provider\Migration as MigrationProvider;

class Console extends Provider
{

    public function consoles()
    {
        return [
            CreatePckgProject::class,
            InitProject::class,
            TestProject::class,
            UpdateProject::class,
            PullProject::class,
            FeatureProject::class,
            ProdProject::class,
            PreprodProject::class,
        ];
    }

    public function providers()
    {
        return [
            MigrationProvider::class,
        ];
    }

}
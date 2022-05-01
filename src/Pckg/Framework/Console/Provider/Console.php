<?php

namespace Pckg\Framework\Console\Provider;

use Pckg\Framework\Console\Command\ClearCache;
use Pckg\Framework\Console\Command\ComposerProject;
use Pckg\Framework\Console\Command\InitProject;
use Pckg\Framework\Console\Command\Sleep;
use Pckg\Framework\Provider;
use Pckg\Migration\Provider\Migration as MigrationProvider;

class Console extends Provider
{
    public function consoles()
    {
        return [
            InitProject::class,
            ComposerProject::class,
            ClearCache::class,
            Sleep::class,
        ];
    }

    public function providers()
    {
        return [
            MigrationProvider::class,
        ];
    }
}

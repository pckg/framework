<?php

namespace Pckg\Framework\Application\Console\Command;

use Pckg\Database\Command\InitDatabase;
use Pckg\Framework\Application\ApplicationInterface;
use Pckg\Framework\Config\Command\InitConfig;
use Pckg\Framework\Locale\Command\Localize;
use Pckg\Framework\Provider\Command\InitAutoloaders;
use Pckg\Framework\Provider\Command\InitProviders;

class Init
{

    protected $application;

    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    public function execute(callable $next)
    {
        chain([
            InitConfig::class,
            Localize::class,
            InitDatabase::class,

            InitAutoloaders::class,
            InitProviders::class,
        ], 'execute', [$this->application]);

        return $next();
    }

}
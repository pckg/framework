<?php

namespace Pckg\Framework\Application\Console\Command;

use Pckg\Database\Command\InitDatabase;
use Pckg\Framework\Application;
use Pckg\Framework\Application\RegisterApplication;
use Pckg\Framework\Config\Command\InitConfig;
use Pckg\Framework\Locale\Command\Localize;
use Pckg\Framework\Router\Command\InitRouter;

class Init
{

    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function execute(callable $next)
    {
        chain([
            InitConfig::class,
            Localize::class,
            InitDatabase::class,
            InitRouter::class,

            RegisterApplication::class,
        ], 'execute', [$this->application]);

        return $next();
    }

}
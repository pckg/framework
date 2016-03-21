<?php

namespace Pckg\Framework\Application\Website\Command;

use Pckg\Database\Command\InitDatabase;
use Pckg\Framework\Application\ApplicationInterface;
use Pckg\Framework\Config\Command\InitConfig;
use Pckg\Framework\I18n\Command\InitI18n;
use Pckg\Framework\Locale\Command\InitLocale;
use Pckg\Framework\Provider\Command\InitAutoloaders;
use Pckg\Framework\Provider\Command\InitProviders;
use Pckg\Framework\Request\Command\InitRequest;
use Pckg\Framework\Request\Session\Command\InitSession;
use Pckg\Framework\Response\Command\InitResponse;
use Pckg\Framework\Router\Command\InitRouter;
use Pckg\Framework\Router\Command\RegisterRoutes;

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
            InitLocale::class,
            InitDatabase::class,
            InitRouter::class,
            RegisterRoutes::class,

            InitAutoloaders::class,
            InitProviders::class,

            InitSession::class,
            InitResponse::class,
            InitRequest::class,
            InitI18n::class,
            InitAssets::class,
        ], 'execute', [$this->application]);

        return $next();
    }

}
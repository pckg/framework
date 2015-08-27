<?php

namespace Pckg\Application;

use Pckg\Application;
use Pckg\Application\Website\Command\InitAssets;
use Pckg\Config\Command\InitConfig;
use Pckg\Database\Command\InitDatabase;
use Pckg\I18n\Command\InitI18n;
use Pckg\Locale\Command\InitLocale;
use Pckg\Request\Command\InitRequest;
use Pckg\Request\Command\RunRequest;
use Pckg\Request\Session\Command\InitSession;
use Pckg\Response\Command\InitResponse;
use Pckg\Response\Command\RunResponse;
use Pckg\Router\Command\InitRouter;

class Website extends Application
{

    protected $initChain = [
        InitConfig::class,
        InitLocale::class,
        InitDatabase::class,
        InitRouter::class,
        InitSession::class,
        InitResponse::class,
        InitRequest::class,
        InitI18n::class,
        InitAssets::class,
    ];

    protected $runChain = [
        RunRequest::class,
        RunResponse::class,
    ];

    public function run()
    {
        $this->middleware();

        return parent::run();
    }

    public function assets()
    {
        return [];
    }

}
<?php

namespace Pckg\Framework\Application;

use Pckg\Database\Command\InitDatabase;
use Pckg\Framework\Application;
use Pckg\Framework\Config\Command\InitConfig;
use Pckg\Framework\I18n\Command\InitI18n;
use Pckg\Framework\Locale\Command\InitLocale;
use Pckg\Framework\Request\Command\InitRequest;
use Pckg\Framework\Request\Command\RunRequest;
use Pckg\Framework\Request\Session\Command\InitSession;
use Pckg\Framework\Response\Command\InitResponse;
use Pckg\Framework\Response\Command\RunResponse;
use Pckg\Framework\Router\Command\InitRouter;

class Api extends Application
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
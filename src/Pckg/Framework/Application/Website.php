<?php

namespace Pckg\Framework\Application;

use Pckg\Database\Command\InitDatabase;
use Pckg\Framework\Application;
use Pckg\Framework\Application\Command\RegisterApplication;
use Pckg\Framework\Application\Website\Command\InitAssets;
use Pckg\Framework\Config\Command\InitConfig;
use Pckg\Framework\Locale\Command\Localize;
use Pckg\Framework\Request\Command\InitRequest;
use Pckg\Framework\Request\Command\RunRequest;
use Pckg\Framework\Request\Session\Command\InitSession;
use Pckg\Framework\Response\Command\InitResponse;
use Pckg\Framework\Response\Command\RunResponse;
use Pckg\Framework\Router\Command\InitRouter;

class Website extends Application
{

    public function inits()
    {
        return [
            InitConfig::class,
            Localize::class,
            InitDatabase::class,
            InitRouter::class,

            RegisterApplication::class,

            InitSession::class,
            InitResponse::class,
            InitRequest::class,
            InitAssets::class,
        ];
    }

    public function runs()
    {
        return [
            RunRequest::class,
            RunResponse::class,
        ];
    }

}
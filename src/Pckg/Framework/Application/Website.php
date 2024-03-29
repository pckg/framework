<?php

namespace Pckg\Framework\Application;

use Pckg\Framework\Application;
use Pckg\Framework\Application\Command\InitDatabase;
use Pckg\Framework\Application\Command\InitEvents;
use Pckg\Framework\Application\Command\RegisterApplication;
use Pckg\Framework\Config\Command\InitConfig;
use Pckg\Framework\Request\Command\InitRequest;
use Pckg\Framework\Request\Command\InitRoute;
use Pckg\Framework\Request\Command\RunRequest;
use Pckg\Framework\Request\Session\Command\InitSession;
use Pckg\Framework\Response\Command\InitResponse;
use Pckg\Framework\Response\Command\RunResponse;
use Pckg\Framework\Router\Command\InitRouter;
use Pckg\Locale\Command\Localize;

class Website extends Application
{
    /**
     * @return string[]
     * Session should be initialized on demand.
     * It should not be initialized for:
     *  - API requests (Bearer-Token, X-Api-Key, ...)
     */
    public function inits()
    {
        return [
            InitConfig::class,
            InitRequest::class,
            InitResponse::class,
            InitEvents::class,
            Localize::class,
            InitRouter::class,
            InitDatabase::class, // can we init it on demand?

            RegisterApplication::class,

            InitRoute::class,
            InitSession::class, // can we init it on demand?
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

<?php namespace Pckg\Framework\Provider;

use Pckg\Framework\Console\Command\ClearCache;
use Pckg\Framework\Console\Command\CreatePckgProject;
use Pckg\Framework\Environment;
use Pckg\Framework\Locale\Lang;
use Pckg\Framework\Provider;
use Pckg\Framework\Request;
use Pckg\Framework\Request\Data\Cookie;
use Pckg\Framework\Request\Data\Server;
use Pckg\Framework\Request\Data\Session;
use Pckg\Framework\Response;
use Pckg\Framework\Router;
use Pckg\Htmlbuilder\Provider\Htmlbuilder;
use Pckg\Migration\Provider\Migration as MigrationProvider;

class Framework extends Provider
{

    public function consoles()
    {
        return [
            CreatePckgProject::class,
            ClearCache::class,
        ];
    }

    public function providers()
    {
        return [
            MigrationProvider::class,
            Htmlbuilder::class,
        ];
    }

    public function viewObjects()
    {
        return [
            '_request'  => Request::class,
            '_response' => Response::class,
            '_server'   => Server::class,
            '_router'   => Router::class,
            '_env'      => Environment::class,
            '_lang'     => Lang::class,
            '_session'  => Session::class,
            '_cookie'   => Cookie::class,
        ];
    }

}
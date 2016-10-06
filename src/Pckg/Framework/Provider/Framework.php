<?php namespace Pckg\Framework\Provider;

use Pckg\Framework\Console\Command\ClearCache;
use Pckg\Framework\Console\Command\CreatePckgProject;
use Pckg\Framework\Provider;
use Pckg\Framework\Request;
use Pckg\Framework\Request\Data\Server;
use Pckg\Framework\Response;
use Pckg\Framework\Router;
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
        ];
    }

    public function viewObjects()
    {
        return [
            '_request'  => Request::class,
            '_response' => Response::class,
            '_server'   => Server::class,
            '_router'   => Router::class,
        ];
    }

}
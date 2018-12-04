<?php namespace Pckg\Framework\Provider;

use Pckg\Framework\Command\Forked;
use Pckg\Framework\Console\Command\ClearCache;
use Pckg\Framework\Console\Command\CreatePckgProject;
use Pckg\Framework\Environment;
use Pckg\Framework\Provider;
use Pckg\Framework\Request;
use Pckg\Framework\Request\Data\Cookie;
use Pckg\Framework\Request\Data\Flash;
use Pckg\Framework\Request\Data\Server;
use Pckg\Framework\Request\Data\Session;
use Pckg\Framework\Response;
use Pckg\Framework\Router;
use Pckg\Framework\Router\Console\ListRoutes;
use Pckg\Framework\View\Handler\RegisterTwigExtensions;
use Pckg\Framework\View\Twig;
use Pckg\Htmlbuilder\Provider\Htmlbuilder;
use Pckg\Locale\LangInterface;
use Pckg\Locale\Provider\Localizer;
use Pckg\Migration\Provider\Migration as MigrationProvider;
use Pckg\Translator\Service\Translator;

class Framework extends Provider
{

    public function consoles()
    {
        return [
            CreatePckgProject::class,
            ClearCache::class,
            ListRoutes::class,
        ];
    }

    public function providers()
    {
        return [
            MigrationProvider::class,
            Htmlbuilder::class,
            Localizer::class,
        ];
    }

    public function viewObjects()
    {
        return [
            '_request'    => Request::class,
            '_response'   => Response::class,
            '_server'     => Server::class,
            '_router'     => Router::class,
            '_env'        => Environment::class,
            '_lang'       => LangInterface::class,
            '_session'    => Session::class,
            '_cookie'     => Cookie::class,
            '_flash'      => Flash::class,
            '_debugBar'   => debugBar(),
            '_translator' => Translator::class,
        ];
    }

    public function listeners()
    {
        return [
            'forked'                            => [
                Forked::class,
            ],
            Twig::class . '.registerExtensions' => [
                RegisterTwigExtensions::class,
            ],
        ];
    }

}
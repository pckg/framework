<?php

namespace Pckg\Framework\Router\Provider;

use Pckg\Framework\Router;
use Pckg\Framework\Router\RouteProviderInterface;
use Pckg\Framework\View\Twig;

/**
 * Class App
 *
 * @package Pckg\Framework\Router\Provider
 * @deprecated
 */
class App implements RouteProviderInterface
{

    protected $app;

    protected $config;

    protected $router;

    public function __construct($app, $config, Router $router)
    {
        $this->app = $app;
        $this->config = $config;
        $this->router = $router;
    }

    public function init()
    {
        /*
         * Basically, what we want to to is load app's route config
         * */

        $path = path('apps') . $this->app . path('ds') . 'Config' . path('ds') . 'router.php';
        //startMeasure('App RouterProvider: ' . $path);
        $phpProvider = new Php(
            [
                'file'   => $path,
                'prefix' => isset($this->config['prefix'])
                    ? $this->config['prefix']
                    : null,
            ]
        );
        $phpProvider->init();

        $this->router->addCachedInit(
            [
                'autoloader' => [path('apps') . $this->app . path('ds') . 'src'],
                'view'       => [path('apps') . $this->app . path('ds') . 'src' . path('ds')],
            ]
        )->writeCache();

        /*
         * And autoloader
         * */
        // @T00D00 - this needs to be called on initialization ...
        autoloader()->add('', path('apps') . $this->app . path('ds') . 'src');

        /*
         * And add to twig?
         * */
        Twig::addDir(path('apps') . $this->app . path('ds') . 'src' . path('ds'), Twig::PRIORITY_APP);
        /*
         * And then you finally realize this should be refactored to some kind of Command or AppInitializator
         * */
        //stopMeasure('App RouterProvider: ' . $path);
    }

    public function getMatch()
    {
        // TODO: Implement getMatch() method.
    }

}
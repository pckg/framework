<?php

namespace Pckg\Router\Provider;

use Pckg\Router\RouteProviderInterface;

class App implements RouteProviderInterface
{

    protected $app;

    protected $config;

    public function __construct($app, $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    public function init()
    {
        /*
         * Basically, what we want to to is load app's route config
         * */

        $path = path('apps') . $this->app . path('ds') . 'Config' . path('ds') . 'router.yml';
        $ymlProvider = new Yml(['file' => $path, 'prefix' => $this->config['prefix']]);
        $ymlProvider->init();

        /*
         * And autoloader
         * */
        autoloader()->add('', path('apps') . $this->app . path('ds') . 'src');

        /*
         * And add to twig?
         * */
        \LFW\View\Twig::addDir(path('apps') . $this->app . path('ds') . 'src' . path('ds'));
        /*
         * And then you finally realize this should be refactored to some kind of Command or AppInitializator
         * */
    }

    public function getMatch()
    {
        // TODO: Implement getMatch() method.
    }


}
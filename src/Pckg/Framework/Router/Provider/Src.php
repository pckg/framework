<?php

namespace Pckg\Framework\Router\Provider;

use Pckg\Concept\Reflect;
use Pckg\Framework\Application;
use Pckg\Framework\Router\RouteProviderInterface;

class Src implements RouteProviderInterface
{

    protected $config;

    protected $src;

    protected $app;

    public function __construct($config, $src, Application $app)
    {
        $this->config = $config;
        $this->src = $src;
        $this->app = $app;
    }

    public function init()
    {
        startMeasure('Src RouterProvider: ' . $this->config['src']);
        foreach ([
                     path('app_src') . $this->config['src'] . path('ds'),
                     path('root') . $this->config['src'] . path('ds'),
                 ] AS $dir) {

            if (is_dir($dir)) {
                context()->get('Config')->parseDir($dir);
            }
        }

        foreach ([
                     path('app_src') . $this->config['src'] . path('ds') . 'Config/router.php',
                     path('root') . $this->config['src'] . path('ds') . 'Config/router.php'
                 ] AS $file) {

            if (!is_file($file)) {
                continue;
            }

            $phpProvider = new Php([
                'file'   => $file,
                'prefix' => isset($this->config['prefix'])
                    ? $this->config['prefix']
                    : null
            ]);
            $phpProvider->init();

            // then we have to find provider
            $class = $this->src . '\Provider\Config';
            if (class_exists($class)) {
                $provider = Reflect::create($class);
                $provider->register();
            }
        }
        stopMeasure('Src RouterProvider: ' . $this->config['src']);
    }

    public function getMatch()
    {
        // TODO: Implement getMatch() method.
    }


}
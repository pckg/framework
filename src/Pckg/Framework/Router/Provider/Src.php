<?php

namespace Pckg\Framework\Router\Provider;

use Pckg\Framework\Application\ApplicationInterface;
use Pckg\Framework\Helper\Reflect;
use Pckg\Framework\Router\RouteProviderInterface;

class Src implements RouteProviderInterface
{

    protected $config;

    protected $src;

    protected $app;

    public function __construct($config, $src, ApplicationInterface $app)
    {
        $this->config = $config;
        $this->src = $src;
        $this->app = $app;
    }

    public function init()
    {
        foreach ([path('app_src') . $this->config['src'] . path('ds'),
                     path('root') . $this->config['src'] . path('ds'),
                     path('src') . $this->config['src'] . path('ds')] AS $dir) {

            if (is_dir($dir)) {
                context()->getBinded('Config')->parseDir($dir);
            }
        }

        foreach ([path('app_src') . $this->config['src'] . path('ds') . 'Config/router.yml',
                     path('root') . $this->config['src'] . path('ds') . 'Config/router.yml',
                     path('src') . $this->config['src'] . path('ds') . 'Config/router.yml'] AS $file) {

            if (!is_file($file)) {
                continue;
            }

            $ymlProvider = new Yml([
                'file' => $file,
                'prefix' => isset($this->config['prefix'])
                    ? $this->config['prefix']
                    : null
            ]);
            $ymlProvider->init();

            // then we have to find provider

            $class = $this->src . '\Provider\Config';
            if (class_exists($class)) {
                $provider = Reflect::create($class);
                $provider->register();
            }
        }
    }

    public function getMatch()
    {
        // TODO: Implement getMatch() method.
    }


}
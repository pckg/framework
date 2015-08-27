<?php

namespace Pckg\Framework\Router\Provider;

use Pckg\Framework\Router\RouteProviderInterface;

class Url implements RouteProviderInterface
{

    protected $url;
    protected $config;
    protected $name;

    public function __construct($url, $config, $name = null)
    {
        $this->url = $url;
        $this->config = $config;
        $this->name = $name
            ? $name
            : isset($config['name'])
                ? $config['name']
                : null;
    }

    public function init()
    {
        router()->add($this->url, $this->config, $this->name);
    }

    public function getMatch()
    {

    }

}
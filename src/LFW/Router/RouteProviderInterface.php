<?php

namespace Pckg\Router;

interface RouteProviderInterface
{

    public function init();

    public function getMatch();

}

abstract class AbstractRouteProvider
{

    protected $name;
    protected $providers = [];

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName($name)
    {
        return $this->name;
    }

    public function addProvider(RouteProviderInterface $provider)
    {
        $this->providers[] = $provider;
    }

}

/*
$urlProvider = new UrlProvider('/some-url', [controller, view, layout], 'name');
*/

/*
$vendorProvider = new (new VendorProvider('Weblab')->setPrefix('/weblab');
$router->addProvider($vendorProvider);
Looks for all packages in vendor folder and its route configuration
*/

/*
Some custom:
*/

class LfwRouteProvider implements RouteProviderInterface
{

    protected $model;

    public function __construct($model, $call)
    {
        $this->model = $model;
    }

    public function init()
    {

    }

    public function getMatch()
    {

    }
}
<?php

namespace Pckg\Router\Provider;

use Pckg\Router\RouteProviderInterface;

class Vendor implements RouteProviderInterface
{

    protected $vendor;

    public function __construct($vendor)
    {
        $this->vendor = $vendor;
    }

    public function init()
    {

    }

    public function getMatch()
    {

    }

}
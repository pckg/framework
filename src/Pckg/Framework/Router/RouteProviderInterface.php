<?php

namespace Pckg\Framework\Router;

interface RouteProviderInterface
{
    public function init();

    public function getMatch();
}

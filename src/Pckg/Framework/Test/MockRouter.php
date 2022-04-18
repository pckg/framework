<?php

namespace Pckg\Framework\Test;

use Pckg\Framework\Config;
use Pckg\Framework\Helper\Context;
use Pckg\Framework\Router;

/**
 * Trait MockRouter
 * @package Pckg\Framework\Test
 * @property Context $context
 */
trait MockRouter
{
    protected function mockRouter(): Router
    {
        $router = new Router($this->context->get(Config::class));
        $this->context->bind(Router::class, $router);

        return $router;
    }

    protected function fetchRouter(): Router
    {
        if ($this->context->exists(Router::class)) {
            return $this->context->get(Router::class);
        }

        return $this->mockRouter();
    }

    protected function registerRoutes(array $routes): Router
    {
        $router = $this->fetchRouter();

        foreach ($routes as $url => $route) {
            $router->add($url, $route);
        }

        return $router;
    }
}

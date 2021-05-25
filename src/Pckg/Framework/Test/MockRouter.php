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
    protected Router $mockedRouter;

    protected function mockRouter(): Router
    {
        if (!isset($this->mockedRouter)) {
            $this->mockedRouter = new Router($this->context->get(Config::class));
            $this->context->bind(Router::class, $this->mockedRouter);
            //$this->mockInContext($this->mockedRouter);
        }

        return $this->mockedRouter;
    }

    protected function fetchRouter(): Router
    {
        if (isset($this->mockedRouter)) {
            return $this->mockedRouter;
        }
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

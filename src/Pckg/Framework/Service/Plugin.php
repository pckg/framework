<?php namespace Pckg\Framework\Service;

use Pckg\Concept\Reflect;

class Plugin
{

    public function make($controller, $method, $params = [], $byRequest = false)
    {
        /**
         * Create controller.
         */
        $controller = Reflect::create($controller, $params);

        /**
         * Call action.
         */
        $view = Reflect::method($controller, (!$byRequest ? $method : (strtolower(request()->method()) . ucfirst($method))) . 'Action', $params);

        return (string)$view;
    }

}
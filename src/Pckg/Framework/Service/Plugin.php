<?php namespace Pckg\Framework\Service;

use Pckg\Concept\Reflect;

class Plugin
{

    public function make($controller, $method, $params = [])
    {
        /**
         * Create controller.
         */
        $controller = Reflect::create($controller, $params);

        /**
         * Call action.
         */
        $view = Reflect::method($controller, $method . 'Action', $params);

        return (string)$view;
    }

}
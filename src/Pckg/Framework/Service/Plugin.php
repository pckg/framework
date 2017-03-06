<?php namespace Pckg\Framework\Service;

use Pckg\Concept\Reflect;
use Throwable;

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
        try {
            $view = (string)Reflect::method($controller, (!$byRequest ? $method : (strtolower(request()->method()) .
                                                                                   ucfirst($method))) . 'Action',
                                            $params);
            return $view;
        } catch (Throwable $e) {
            if (prod()) {
                return null;
            }

            throw $e;
        }
    }

}
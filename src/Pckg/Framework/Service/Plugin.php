<?php namespace Pckg\Framework\Service;

use Pckg\Concept\Reflect;
use Throwable;

class Plugin
{

    public function make($controller, $method, $params = [], $byRequest = true, $toString = true, $headToGet = true)
    {
        /**
         * Create controller.
         */
        $controller = Reflect::create($controller, $params);

        /**
         * Call action.
         */
        try {
            /**
             * Prepend request method.
             */
            if ($byRequest === true) {
                $httpMethod = strtolower(request()->method());
                if ($headToGet && $httpMethod == 'head') {
                    $httpMethod = 'get';
                }
                $method = $httpMethod . ucfirst($method);
            }

            /**
             * Add Action suffix.
             */
            $method .= 'Action';

            /**
             * Get action response.
             */
            $view = Reflect::method($controller, $method, $params);

            /**
             * Convert to string if required.
             */
            if ($toString) {
                $view = (string)$view;
            }

            return $view;
        } catch (Throwable $e) {
            /**
             * This is non-critical error, we can display empty output on production environment.
             */
            if (prod()) {
                return null;
            }

            throw $e;
        }
    }

}
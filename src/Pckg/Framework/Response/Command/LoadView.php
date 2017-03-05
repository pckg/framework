<?php namespace Pckg\Framework\Response\Command;

use Exception;
use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Concept\Reflect;
use Pckg\Framework\Request;
use Pckg\Framework\Router;

class LoadView extends AbstractChainOfReponsibility
{

    protected $view;

    protected $data;

    protected $controller;

    protected $request;

    protected $router;

    public function __construct(Request $request, Router $router)
    {
        $this->request = $request;
        $this->router = $router;
    }

    public function set($view, $data, $controller)
    {
        $this->view = $view;
        $this->data = $data;
        $this->controller = $controller;

        return $this;
    }

    public function execute()
    {
        $viewHttp = $this->request->isPost()
            ? 'post' . ucfirst($this->view)
            : 'get' . ucfirst($this->view);

        $result = null;
        $data = $this->getResolved();

        if (!method_exists($this->controller, $viewHttp . "Action")) {
            throw new Exception('Method ' . $viewHttp . 'Action() does not exist in ' . get_class($this->controller));
        }

        /**
         * Call main route action.
         */
        $result = Reflect::method($this->controller, $viewHttp . "Action", array_merge($this->data, $data));

        return $result;
    }

    protected function getResolved()
    {
        $router = $this->router->get();

        $data = $this->router->get('data');
        if (isset($router['resolvers'])) {
            foreach ($router['resolvers'] as $urlKey => $resolver) {
                $realResolver = is_object($resolver)
                    ? $resolver
                    : resolve($resolver);
                $resolved = $realResolver->resolve($router[$urlKey] ?? $this->router->getCleanUri());

                if (is_string($urlKey)) {
                    $data[$urlKey] = $resolved;
                }

                $data[] = $resolved;
                if (!is_int($urlKey)) {
                    $this->router->resolve($urlKey, $resolved);
                    /**
                     * Remove resolved key.
                     * Why? Can we delete it?
                     */
                    if (isset($data[$urlKey])) {
                        //unset($data[$urlKey]);
                    }
                }
            }
        }

        return $data;
    }

}
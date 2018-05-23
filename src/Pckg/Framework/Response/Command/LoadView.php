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
        $viewHttp = strtolower($this->request->method()) . ucfirst($this->view);

        $result = null;

        if (!method_exists($this->controller, $viewHttp . "Action")) {
            throw new Exception('Method ' . $viewHttp . 'Action() does not exist in ' . get_class($this->controller));
        }

        /**
         * Call main route action.
         */
        $result = Reflect::method($this->controller, $viewHttp . "Action", $this->data);

        return $result;
    }

}
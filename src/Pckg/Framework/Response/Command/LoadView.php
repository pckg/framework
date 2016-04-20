<?php

namespace Pckg\Framework\Response\Command;


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

    /**
     * @T00D00
     *  - remove Prepare method + call only request action get/post/delete/put...
     *  - parameters should be solved by route resolver ...
     */
    public function execute()
    {
        if (method_exists($this->controller, $this->view . "Prepare")) {
            Reflect::method($this->controller, $this->view . "Prepare", $this->data);
        }

        $viewHttp = $this->request->isPost()
            ? 'post' . ucfirst($this->view)
            : 'get' . ucfirst($this->view);

        $result = null;
        $router = $this->router->get();
        $data = [];
        if (isset($router['resolvers'])) {
            foreach ($router['resolvers'] as $urlKey => $resolver) {
                $data[] = Reflect::create($resolver)->resolve(null);
            }
        }

        if (method_exists($this->controller, $viewHttp . "Action")) {
            $result = Reflect::method($this->controller, $viewHttp . "Action", array_merge($this->data, $data));

        } else if (method_exists($this->controller, $this->view . "Action")) {
            $result = Reflect::method($this->controller, $this->view . "Action", $this->data);

        }

        return $result;
    }

}
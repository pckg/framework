<?php

namespace Pckg\Response\Command;


use Exception;
use Pckg\Concept\AbstractChainOfReponsibility;


use Pckg\Reflect;
use Pckg\Request;

class LoadView extends AbstractChainOfReponsibility
{

    protected $view;

    protected $data;

    protected $controller;

    protected $request;

    public function set($view, $data, $controller) {
        $this->view = $view;
        $this->data = $data;
        $this->controller = $controller;

        return $this;
    }

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function execute()
    {
        if (method_exists($this->controller, $this->view . "Prepare"))
            Reflect::method($this->controller, $this->view . "Prepare", $this->data);

        $viewHttp = $this->request->isPost()
            ? 'post' . ucfirst($this->view)
            : 'get' . ucfirst($this->view);

        $result = null;

        if (method_exists($this->controller, $viewHttp . "Action")) {
            return Reflect::method($this->controller, $viewHttp . "Action", $this->data);

        } else if (method_exists($this->controller, $this->view . "Action")) {
            return Reflect::method($this->controller, $this->view . "Action", $this->data);

        }
    }

}
<?php

namespace Pckg\Framework\Response\Command;

use Pckg\Concept\AbstractChainOfReponsibility;

use Pckg\Database\Helper\Convention;
use Pckg\Reflect;
use Pckg\Framework\Response;
use Pckg\Framework\View\ViewInterface;

class ProcessRouteMatch extends AbstractChainOfReponsibility
{

    protected $match;

    protected $view, $controller;

    protected $response;

    protected $loadView;

    public function __construct($match, Response $response, LoadView $loadView)
    {
        $this->match = $match;
        $this->response = $response;
        $this->loadView = $loadView;
    }

    public function execute()
    {
        $this->loadController($this->match['controller']);

        $viewData = $this->handleView($this->match);

        $this->response->handle($viewData);
    }

    public function handleView($match)
    {
        $viewData = $this->loadView->set($match['view'], [], $this->controller)->execute();

        if ($viewData instanceof ViewInterface) {
            // parse layout into view
            return $viewData;

        } else if (is_string($viewData)) {
            // print view as content
            return ["content" => $viewData];

        } else if (is_null($viewData) || is_int($viewData) || is_bool($viewData)) {
            // without view
            return null;

        } else if (is_array($viewData)) {
            // send data to layout ;-) // @T00D00 - layout doesn't exit anymore
            return $viewData;

        }

        throw new \Exception("View is unknown type" . var_dump($viewData));
    }

    public function loadController($controller)
    {
        if (strpos($controller, 'Controller')) {
            $c = $controller;
        } else {
            $controller = explode(":", $controller);
            $controller[1] = isset($controller[1]) ? $controller[1] : substr(strrchr($controller[0], "\\"), 1);

            $c = '\\' . Convention::toCamel($controller[0]) . '\Controller\\' . Convention::toCamel($controller[1]);
        }

        $reflect = Reflect::create($c);

        if (!$this->controller || get_class($this->controller) != get_class($reflect)) $this->controller = $reflect;

        return $reflect;
    }

}
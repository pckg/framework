<?php

namespace Pckg\Framework\Response\Command;

use Exception;
use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Concept\Reflect;
use Pckg\Database\Helper\Convention;
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
        /**
         * Apply middlewares.
         */
        if ($middlewares = $this->response->getMiddlewares()) {
            chain($middlewares, 'execute');
        }

        $this->controller = Reflect::create($this->match['controller']);

        $viewData = $this->handleView($this->match);

        $this->response->setViewData($viewData);

        /**
         * Apply afterwares/decorators.
         */
        if ($afterwares = $this->response->getAfterwares()) {
            chain($afterwares, 'execute', [$this->response]);
        }
    }

    public function handleView($match)
    {
        $viewData = $this->loadView->set($match['view'], [], $this->controller)->execute();

        if ($viewData instanceof ViewInterface) {
            // parse layout into view
            return $viewData;

        } else if (is_string($viewData)) {
            // print view as content
            return $viewData;

        } else if (is_null($viewData) || is_int($viewData) || is_bool($viewData)) {
            // without view
            return null;

        } else if (is_array($viewData)) {
            // send data to layout ;-) // @T00D00 - layout doesn't exit anymore
            return $viewData;

        } else if (is_object($viewData) && method_exists($viewData, '__toString')) {
            return (string)$viewData;
        }

        throw new Exception("View is unknown type ");
    }

}
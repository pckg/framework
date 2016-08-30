<?php

namespace Pckg\Framework\Response\Command;

use Exception;
use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Concept\Reflect;
use Pckg\Framework\Response;
use Pckg\Framework\Response\Exception\TheEnd;
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
        try {
            /**
             * Apply global middlewares.
             */
            if ($middlewares = $this->response->getMiddlewares()) {
                chain($middlewares, 'execute');
            }

            /**
             * Apply route middlewares.
             */
            if (isset($this->match['middlewares'])) {
                chain($this->match['middlewares'], 'execute');
            }

            /**
             * Create controller object.
             */
            $this->controller = Reflect::create($this->match['controller']);
            $response = $this->loadView->set($this->match['view'], [], $this->controller)->execute();

            $output = $this->parseViewToString($response);

            $this->response->setOutput($output);

            /**
             * Apply global afterwares/decorators.
             */
            if ($afterwares = $this->response->getAfterwares()) {
                chain($afterwares, 'execute', [$this->response]);
            }

            /**
             * Apply route afterwares/decorators.
             */
            if (isset($this->match['afterwares'])) {
                chain($this->match['afterwares'], 'execute', [$this->response]);
            }
        } catch (TheEnd $e) {
            exit;

        } catch (Exception $e) {
            /**
             * @T00D00 - this should be somewhere else
             */
            if (request()->isAjax() && request()->isPost()) {
                $this->response->setOutput(
                    json_encode(
                        [
                            'exception' => $e->getMessage(),
                            'error'     => true,
                            'success'   => false,
                        ]
                    )
                );
            }
        }
    }

    public function parseViewToString($viewData)
    {
        if ($viewData instanceof ViewInterface) {
            // parse layout into view
            return $viewData->autoparse();

        } else if (is_string($viewData) || is_array($viewData)) {
            // print view as content
            return $viewData;

        } else if (is_null($viewData) || is_int($viewData) || is_bool($viewData)) {
            // without view
            return null;

        } else if (is_object($viewData) && method_exists($viewData, '__toString')) {
            return (string)$viewData;
        }

        throw new Exception("View is unknown type " . (is_object($viewData) ? get_class($viewData) : ''));
    }

}
<?php

namespace Pckg\Framework\Response\Command;

use Exception;
use Pckg\Collection;
use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Concept\Reflect;
use Pckg\Framework\Response;
use Pckg\Framework\Response\Exception\TheEnd;
use Pckg\Framework\View;
use Pckg\Framework\View\ViewInterface;
use Throwable;

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

            /**
             * Get main action response.
             */
            $response = $this->loadView->set($this->match['view'], [], $this->controller)->execute();

            //try {
                $output = $this->parseView($response);

            /*} catch (Throwable $e) {
                // @T00D00 - log!
                if (dev()) {
                    $output = exception($e);

                } else {
                    throw $e;

                }
            }*/

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

        } catch (Throwable $e) {
            $this->response->code(500);
            /**
             * @T00D00 - this should be somewhere else
             */
            if (request()->isAjax() && request()->isPost()) {
                $this->response->respond(
                    [
                        'exception' => exception($e),
                        'error'     => true,
                        'success'   => false,
                    ]
                );
            } else {
                throw $e;
            }
        }
    }

    public function parseView($viewData)
    {
        if (is_object($viewData)) {
            if ($viewData instanceof ViewInterface) {
                // parse layout into view
                return $viewData->autoparse();
            } else if ($viewData instanceof Collection) {
                // convert to array
                return $viewData->toArray();
            } else if (method_exists($viewData, '__toString')) {
                return (string)$viewData;

            }

        } else if (is_string($viewData) || is_array($viewData)) {
            // print view as content
            return $viewData;

        } else if (is_null($viewData) || is_int($viewData) || is_bool($viewData)) {
            // without view
            return null;

        }

        throw new Exception("View is unknown type " . (is_object($viewData) ? get_class($viewData) : ''));
    }

}
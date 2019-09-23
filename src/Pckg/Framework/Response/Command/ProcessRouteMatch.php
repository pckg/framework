<?php

namespace Pckg\Framework\Response\Command;

use Exception;
use Pckg\Collection;
use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Concept\Reflect;
use Pckg\Framework\Exception\NotFound;
use Pckg\Framework\Exception\Unauthorized;
use Pckg\Framework\Response;
use Pckg\Framework\Response\Exception\TheEnd;
use Pckg\Framework\Router\Command\ResolveDependencies;
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
        $e = null;
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
             * Resolve dependencies.
             */
            $resolved = (new ResolveDependencies(router()->get('resolvers')))->execute();

            if (is_only_callable($this->match['view'])) {
                /**
                 * Simple action.
                 */
                $response = Reflect::call($this->match['view'], $resolved);
            } else {
                /**
                 * Create controller object.
                 */
                $this->controller = Reflect::create($this->match['controller']);

                /**
                 * Get main action response.
                 */
                $response = $this->loadView->set($this->match['view'], $resolved, $this->controller)->execute();
            }

            if (!$this->response->hasResponded()) {
                $output = $this->parseView($response);

                $this->response->setOutput($output);
            }

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
            /**
             * Catch end of execution.
             */
            exit;
        } catch (NotFound $e) {
            /**
             * Set response code to 404.
             */
            $this->response->code(404);
        } catch (Unauthorized $e) {
            /**
             * Set response code to 401.
             */
            $this->response->code(401);
        } catch (Throwable $e) {
            /**
             * Set response code to 500.
             */
            $code = $this->response->getCode();
            if (!$code || in_array(substr($code, 0, 1), [2, 3, 4])) {
                $this->response->code(500);
            }
        } finally {
            /**
             * Yeeey, no exception, return with execution
             */
            if (!$e) {
                return;
            }

            /**
             * @T00D00 - this should be somewhere else
             */
            if (request()->isAjax() && request()->isPost()) {
                $this->response->respond(
                    [
                        'message'   => $e->getMessage(),
                        'exception' => implicitDev() ? exception($e) : null,
                        'error'     => true,
                        'success'   => false,
                    ]
                );

                return;
            }

            /**
             * Finally, throw exception.
             */
            throw $e;
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
            } else if ($viewData instanceof \stdClass) {
                return json_encode($viewData);
            } else if ($viewData instanceof Response) {
                return $this->parseView($viewData->getOutput());
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
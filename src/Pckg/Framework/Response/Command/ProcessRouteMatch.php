<?php

namespace Pckg\Framework\Response\Command;

use Exception;
use Pckg\Collection;
use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Concept\Reflect;
use Pckg\Framework\Exception\Bad;
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
            $dispatcher = dispatcher();
            $dispatcher->trigger(ProcessRouteMatch::class . '.runningMiddlewares');
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
            $dispatcher->trigger(ProcessRouteMatch::class . '.dependenciesResolved');

            /**
             * Check for CORS?
             */
            $isOptionsRequest = request()->isOptions();
            $processOptions = function () use ($isOptionsRequest) {
                if (!$isOptionsRequest) {
                    return;
                };

                $this->response->code(204)->respond();
            };
            if (is_only_callable($this->match['view'])) {
                /**
                 * Simple action will take all requests - GET, POST, DELETE, ...
                 */
                $processOptions();
                $response = Reflect::call($this->match['view'], $resolved);
            } elseif (array_key_exists('controller', $this->match)) {
                /**
                 * Create controller object.
                 */
                $this->controller = Reflect::create($this->match['controller']);

                /**
                 * Check for OPTIONS.
                 */
                $method = strtolower(request()->header('Access-Control-Request-Method')) . ucfirst($this->match['view']) . 'Action';
                if (method_exists($this->controller, $method)) {
                    $processOptions();
                } elseif ($isOptionsRequest) {
                    throw new Exception('Method not supported');
                }

                /**
                 * Get main action response.
                 * This is where Resolvers may already Respond with final response.
                 */
                $response = $this->loadView->set($this->match['view'], $resolved, $this->controller)->execute();
            } else {
                /**
                 * Vue route or similar?
                 */
                $processOptions();
                $response = $this->match['view'];
            }

            /**
             * Trigger an event on successful response.
             */
            if (!$this->response->hasResponded()) {
                $dispatcher->trigger(ProcessRouteMatch::class . '.ran');
                $output = $this->parseView($response);

                $this->response->setOutput($output);
            }

            /**
             * Apply global afterwares/decorators.
             */
            $dispatcher->trigger(ProcessRouteMatch::class . '.runningAfterwares');
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
        } catch (Response\MockStop $e) {
            /**
             * Fake end.
             */
            throw $e;
        } catch (NotFound $e) {
            /**
             * Set response code to 404.
             */
            $code = $this->response->getCode();
            $this->response->code(404);
            error_log('CODE 404: ' . exception($e));
        } catch (Bad $e) {
            /**
             * Set response code to 400.
             */
            $code = $this->response->getCode();
            $this->response->code(400);
            error_log('CODE 400: ' . exception($e));
        } catch (Throwable $e) {
            /**
             * Set response code to 500.
             */
            $code = $this->response->getCode();
            if (!$code || in_array(substr($code, 0, 1), [2, 3])) {
                $code = 500;
                $this->response->code($code);
            }
            error_log('CODE ' . $code . ': ' . exception($e));
        } finally {
            /**
             * Yeeey, no exception, return with execution
             */
            if (!$e || get_class($e) === Response\MockStop::class) {
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

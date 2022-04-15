<?php

namespace Pckg\Framework\Response\Command;

use Exception;
use Pckg\Collection;
use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Concept\Reflect;
use Pckg\Framework\Exception\Bad;
use Pckg\Framework\Exception\NotFound;
use Pckg\Framework\Exception\Unauthorized;
use Pckg\Framework\Request;
use Pckg\Framework\Response;
use Pckg\Framework\Response\Exception\TheEnd;
use Pckg\Framework\Router\Command\ResolveDependencies;
use Pckg\Framework\View\ViewInterface;
use Throwable;

class ProcessRouteMatch extends AbstractChainOfReponsibility
{
    protected $controller;

    protected $request;

    protected $response;

    protected $loadView;

    public function __construct(LoadView $loadView, Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->loadView = $loadView;
    }

    public function execute()
    {
        $e = null;
        try {
            /**
             * Get defaults.
             */
            $match = $this->request->getMatch();

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
            if (isset($match['middlewares'])) {
                chain($match['middlewares'], 'execute');
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

            if (is_only_callable($match['view'])) {
                /**
                 * Simple action will take all requests - GET, POST, DELETE, ...
                 */
                $processOptions();
                $response = Reflect::call($match['view'], $resolved);
                $this->response->setOutput($response);
            } elseif (array_key_exists('controller', $match)) {
                /**
                 * Create controller object.
                 */
                $this->controller = Reflect::create($match['controller']);

                /**
                 * Check for OPTIONS.
                 */
                $method = strtolower(request()->header('Access-Control-Request-Method')) . ucfirst($match['view']) . 'Action';
                if (method_exists($this->controller, $method)) {
                    $processOptions();
                } elseif ($isOptionsRequest) {
                    throw new Exception('Method not supported');
                }

                /**
                 * Get main action response.
                 * This is where Resolvers may already Respond with final response.
                 */
                $response = $this->loadView->set($match['view'], $resolved, $this->controller)->execute();
                if ($response !== $this->response) {
                    $this->response->setOutput($response);
                }
            } else {
                /**
                 * Vue route or similar?
                 */
                $processOptions();
                $response = $match['view'];
                $this->response->setOutput($response);
            }

            /**
             * Trigger an event on successful response.
             * Transform output to string or array.
             */
            if (!$this->response->hasResponded()) {
                $dispatcher->trigger(ProcessRouteMatch::class . '.ran');
                $this->response->reparseOutput();
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
            if (isset($match['afterwares'])) {
                chain($match['afterwares'], 'execute', [$this->response]);
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
        } catch (Bad $e) {
            /**
             * Set response code to 400.
             */
            $code = $this->response->getCode();
            $this->response->code(400);
        } catch (Throwable $e) {
            /**
             * Set response code to 500.
             */
            $code = $this->response->getCode();
            if (!$code || in_array(substr($code, 0, 1), [2, 3])) {
                $code = 500;
                $this->response->code($code);
            }
        } finally {
            if (!response()->getOutput()) {
                response()->setOutput([
                    'error' => true,
                    'success' => false,
                    'message' => $e ? $e->getMessage() : 'No output',
                ]);
            }
            //error_log('ERROR, response code ' . $this->response->getCode() . ': ' . exception($e));
            /**
             * Yeeey, no exception, return with execution
             */
            if (!$e || get_class($e) === Response\MockStop::class) {
                return;
            }

            /**
             * Finally, throw exception.
             */
            //db(10);die();
            throw $e;
        }
    }
}

<?php

namespace Pckg\Framework\Request\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Framework\Middleware\ShowNiceErrorPage;
use Pckg\Framework\Request;
use Pckg\Framework\Router;
use Pckg\Framework\Router\Command\ResolveRoute;

class InitRoute extends AbstractChainOfReponsibility
{
    protected $request;

    protected $context;

    protected $router;

    public function __construct(Request $request, Router $router)
    {
        $this->request = $request;
        $this->router = $router;
    }

    public function execute(callable $next)
    {
        trigger(Request::class . '.initializing', [$this->request]);

        $url = $this->request->getUrl();

        /**
         * Check that router has any routes defined.
         */
        if (!$this->router->getRoutes()) {
            if ($this->request->isGet()) {
                return response()->respond(view('vendor/pckg/framework/src/Pckg/Framework:default/new-project'));
            }
            return response()->respond(['success' => false, 'error' => true, 'message' => 'No routes']);
        }

        $match = (new ResolveRoute($this->router, $url, first(server('HTTP_HOST'), config('domain'))))->execute();

        if (!$match) {
            /**
             * Resolve without domain.
             */
            message('Match by domain not found, matching without domain');
            $match = (new ResolveRoute($this->router, $url))->execute();
        }

        if (!$match) {
            message('No route match found');
            response()->code(404);
            trigger(ResolveRoute::class . '.notFound');

            $match = [
                'view' => function () {
                    if ($output = response()->getOutput()) {
                        return $output;
                    }

                    if (request()->isJson() || request()->isAjax()) {
                        return (new ShowNiceErrorPage())->handleJson(['message' => 'Not found']);
                    }

                    return (new ShowNiceErrorPage())->handlePartial();
                },
                'tags' => ['layout:frontend'],
                'name' => 'error',
                'url' => '/#error',
                'method' => 'GET',
                'resolvers' => [],
            ];
        }

        $match = array_merge($match['data'] ?? [], $match);

        /**
         * Do we need to set it in request and router?
         */
        $this->router->setData($match);
        $this->request->setMatch($match);
        message('Match found ' . json_encode($match));

        trigger(Request::class . '.initialized', [$this->request]);

        return $next();
    }
}

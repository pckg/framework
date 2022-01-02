<?php

namespace Pckg\Framework\Test;

use Pckg\Concept\Context;
use Pckg\Framework\Config;
use Pckg\Framework\Environment;
use Pckg\Framework\Request;
use Pckg\Framework\Response;
use Pckg\Framework\Router;
use Pckg\Framework\Stack;
use Pckg\Queue\Service\Driver\Mock;

trait MockFramework
{
    use MockContext;

    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var \Pckg\Framework\Helper\Context
     */
    protected $context;

    protected $recreateContext = true;

    protected function setRecreateContext($recreate = false)
    {
        $this->recreateContext = $recreate;

        return $this;
    }

    protected function mockEnvironment()
    {
        $context = $this->mockContext();

        /**
         * Create, bind and register the environment.
         */
        $config = new Config();
        $environment = new Environment\Production($config, $context);
        $context->bind(Environment::class, $environment);
        $environment->register();

        /**
         * Configure for mocking.
         */
        $singletones = $config->get('pckg.reflect.singletones', []);
        $mockSingletones = [
            Response::class => Response\MockResponse::class,
            Request::class => Request\MockRequest::class,
        ];
        $config->set('pckg.reflect.singletones', $mockSingletones + $singletones);
        $config->set('pckg.session.driver', Request\Data\SessionDriver\MockDriver::class);

        return [$context, $config];
    }

    protected function mockFramework($url = '/', $method = 'GET')
    {
        if (!$this->recreateContext && isset($this->context)) {
            $context = $this->context;
            $config = $context->get(Config::class);
        } else {
            [$context, $config] = $this->mockEnvironment();
        }

        /**
         * Init request
         */
        $server = [
            'argv' => '',
            'HTTP_HOST' => 'localhost',
            'REQUEST_URI' => $url,
            'HTTP_X_REQUESTED_WITH' => '',
            'REQUEST_SCHEME' => 'HTTPS',
            'HTTP_USER_AGENT' => 'X-Test',
            'HTTP_REFERER' => '',
            'REQUEST_METHOD' => $method,
        ];
        $router = new Router($config);
        $context->bind(Router::class, $router);

        /**
         * @var $request Request\MockRequest
         */
        $request = resolve(Request::class);
        resolve(Response::class);

        $currentServer = $request->server()->all();
        $request->server()->setData($server + $currentServer);

        $request->fetchUrl();

        /*$request = new Request();
        $context->bind(Request::class, $request);
        $request->setConstructs([], [], $server, [], [], [], []);

        $response = new Response\MockResponse();
        $context->bind(Response::class, $response);*/

        return $context;
    }

    protected function mergeRequestHeaders(Context $context, array $headers)
    {
        $request = $context->get(Request::class);
        $request->setHeaders($headers + $request->getHeaders());

        return $this;
    }

    protected function mock(): MockRequest
    {
        // @phpstan-ignore-next-line
        return (new MockRequest($this, $this->app));
    }

    protected function runExtensionDecorations($decoration)
    {
        if (!is_string($decoration)) {
            return;
        }

        foreach (get_class_methods($this) as $method) {
            if (strpos($method, $decoration) !== 0 || strpos($method, 'Extension') === false) {
                continue;
            }
            $this->{$method}();
        }
    }

    public function _before()
    {
        $this->runExtensionDecorations('_before');

        return $this;
    }

    public function _after()
    {
        $this->runExtensionDecorations('_after');

        return $this;
    }
}

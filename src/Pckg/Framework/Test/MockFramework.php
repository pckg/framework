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

    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Context
     */
    protected $context;

    protected $recreateContext = true;

    protected function setRecreateContext($recreate = false)
    {
        $this->recreateContext = $recreate;

        return $this;
    }

    /**
     * @return mixed
     */
    protected function getPckgBootstrap()
    {
        $file = 'vendor/pckg/framework/src/bootstrap.php';
        if (!is_file($file)) {
            $file = 'src/bootstrap.php';
        }
        return include $file;
    }

    public function mockEnvironment()
    {
        /**
         * Make sure that App is fully loaded?
         * We would like to mock the environment, application and request.
         */
        $bootstrap = $this->getPckgBootstrap();

        /**
         * Only bootstrap and create context. Do not create environment or init the application.
         * @var $context \Pckg\Concept\Context|\Pckg\Framework\Helper\Context
         */
        $originalContext = context();
        Stack::$providers = [];
        $this->context = $context = $bootstrap(null, null);

        $originalContext->bind(Context::class, $context);
        $originalContext->bind(\Pckg\Framework\Helper\Context::class, $context);

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

    public function mockFramework($url = '/', $method = 'GET')
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

    public function mock()
    {
        // @phpstan-ignore-next-line
        return (new MockRequest($this, $this->app));
    }

    public function runExtensionDecorations($decoration)
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

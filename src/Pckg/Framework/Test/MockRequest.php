<?php namespace Pckg\Framework\Test;

use Codeception\Test\Unit;
use Pckg\Concept\Context;
use Pckg\Framework\Application;
use Pckg\Framework\Config;
use Pckg\Framework\Environment;
use Pckg\Framework\Request;
use Pckg\Framework\Response;
use Pckg\Framework\Router;

class MockRequest
{

    /**
     * @var Unit
     */
    protected $test;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var \Throwable
     */
    protected $exception;

    function __construct(Unit $test)
    {
        $this->test = $test;
    }

    /**
     * @param $code
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function assertResponseCode($code)
    {
        $response = $this->context->get(Response::class);
        $this->test->assertEquals($code, $response->getCode(), 'Response code not ' . $code);

        return $this;
    }

    /**
     * @param $url
     * @param callable|null $configurator
     * @return $this
     */
    public function httpGet($url, callable $configurator = null)
    {
        return $this->fullHttpRequest($url, $configurator, 'GET');
    }

    /**
     * @param $url
     * @param callable|null $configurator
     * @return $this
     */
    public function httpDelete($url, callable $configurator = null)
    {
        return $this->fullHttpRequest($url, $configurator, 'DELETE');
    }

    /**
     * @param $url
     * @param array $post
     * @param callable|null $configurator
     * @return $this
     */
    public function httpPost($url, array $post = [], callable $configurator = null)
    {
        $newConfigurator = function (Context $context) use ($post, $configurator) {
            $context->get(Request::class)->setPost($post);
            if ($configurator) {
                $configurator($context);
            }
        };
        return $this->fullHttpRequest($url, $newConfigurator, 'POST');
    }

    /**
     * @param $url
     * @param callable|null $configurator
     * @param string $method
     */
    public function fullHttpRequest($url, callable $configurator = null, $method = 'GET')
    {
        /**
         * Make sure that App is fully loaded?
         * We would like to mock the environment, application and request.
         */
        $bootstrap = $this->getPckgBootstrap();

        /**
         * Only bootstrap and create context. Do not create environment or init the application.
         * @var $context \Pckg\Concept\Context
         */
        $this->context = $context = $bootstrap(null, null);
        //context()->bind(Context::class, $context);

        $config = new Config();
        $context->bind(Config::class, $config);

        //$router = new Router($config);
        //$context->bind(Router::class, $router);

        /**
         * Create and bind the Environment. Do not register it.
         */
        $environment = new Environment\Production($config, $context);
        $context->bind(Environment::class, $environment);
        $environment->register();

        /**
         * Now we can boot the Context and init the Application.
         */
        $this->application = $environment->createApplication($context, 'scintilla');

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
        $request = new Request();
        $request->setConstructs([], [], $server, [], [], [], []);
        $context->bind(Request::class, $request);

        $response = new Response\MockResponse();
        $context->bind(Response::class, $response);

        /**
         * This is where request and response are initialized.
         */
        if ($configurator) {
            $configurator($this->context);
        }

        $this->application->init();

        /**
         * Init the Application.
         */
        try {
            (new Request\Command\RunRequest($request))->execute(function () {
            });
        } catch (\Throwable $e) {
            $this->exception = $e;
        }

        return $this;
    }

    /**
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return Response
     * @throws \Exception
     */
    public function getResponse()
    {
        return $this->getContext()->get(Response::class);
    }

    /**
     * @return mixed|string|array|null
     * @throws \Exception
     */
    public function getOutput()
    {
        return $this->getResponse()->getOutput;
    }

    /**
     * @return mixed
     */
    protected function getPckgBootstrap()
    {
        return include "vendor/pckg/framework/src/bootstrap.php";
    }

}
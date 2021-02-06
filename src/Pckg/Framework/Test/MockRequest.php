<?php

namespace Pckg\Framework\Test;

use Codeception\Test\Unit;
use Pckg\Concept\Context;
use Pckg\Framework\Application;
use Pckg\Framework\Config;
use Pckg\Framework\Environment;
use Pckg\Framework\Request;
use Pckg\Framework\Response;
use Pckg\Framework\Router;
use Pckg\Framework\Stack;

class MockRequest
{
    use MockFramework;

    /**
     *
     */
    const MODE_JSON = 'JSON';

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

    /**
     * @var string
     */
    protected $app;

    /**
     * MockRequest constructor.
     * @param Unit $test
     * @param $app
     */
    public function __construct($test, $app)
    {
        $this->test = $test;
        $this->app = $app;
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
     * @param $code
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function assertResponseHas($key)
    {
        $response = $this->context->get(Response::class)->getOutput();

        $this->test->assertEquals(true, !!(is_array($response) ? ($response[$key] ?? null) : (json_decode($response, true)[$key] ?? null)), 'Response does not have a ' . $key);

        return $this;
    }

    /**
     * @param $code
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function assertResponseContains($value)
    {
        $response = $this->context->get(Response::class)->getOutput();

        $this->test->assertEquals(true, strpos($response, $value) >= 0, 'Response does not contain ' . $value);

        return $this;
    }

    /**
     * Add JSON support.
     *
     * @param callable|null $configurator
     * @param null $mode
     * @return callable|\Closure|null
     */
    public function modifyConfigurator(callable $configurator = null, $mode = null)
    {
        if (!$mode && !$configurator) {
            return null;
        } else if (!$mode) {
            return $configurator;
        }

        return function (Context $context) use ($configurator) {
            /**
             * @var $request Request
             */
            $request = $context->get(Request::class);
            $request->server()->set('HTTP_X_REQUESTED_WITH', 'xmlhttprequest');
            $request->server()->set('HTTP_X_PCKG_CSRF', metaManager()->getCsrfValue());
            $request->server()->set('HTTP_REFERER', 'https://localhost');
            $request->server()->set('HTTP_ORIGIN', 'localhost:99999');
            $request->setHeaders([
                'Accept' => 'application/json',
                'X-Pckg-CSRF' => metaManager()->getCsrfValue(),
            ]);
            if ($configurator) {
                $configurator($context);
            }
        };
    }

    /**
     * @param $url
     * @param callable|null $configurator
     * @return $this
     */
    public function httpGet($url, callable $configurator = null, $mode = null)
    {
        return $this->fullHttpRequest($url, $this->modifyConfigurator($configurator, $mode), 'GET');
    }

    /**
     * @param $url
     * @param callable|null $configurator
     * @return $this
     */
    public function httpGetJson($url, callable $configurator = null)
    {
        return $this->fullHttpRequest($url, $this->modifyConfigurator($configurator, static::MODE_JSON), 'GET');
    }

    /**
     * @param $url
     * @param callable|null $configurator
     * @return $this
     */
    public function httpDelete($url, callable $configurator = null)
    {
        return $this->fullHttpRequest($url, $this->modifyConfigurator($configurator), 'DELETE');
    }

    /**
     * @param $url
     * @param array $post
     * @param callable|null $configurator
     * @return $this
     */
    public function httpPost($url, array $post = [], callable $configurator = null)
    {
        return $this->fullHttpRequest($url, function (Context $context) use ($post, $configurator) {
            $configurator = $this->modifyConfigurator($configurator, static::MODE_JSON);
            $configurator($context);
            $context->get(Request::class)->setPost($post);
        }, 'POST');
    }

    /**
     * @param $url
     * @param callable|null $configurator
     * @param string $method
     */
    public function fullHttpRequest($url, callable $configurator = null, $method = 'GET')
    {
        $context = $this->mockFramework();
        $environment = $context->get(Environment::class);

        /**
         * Now we can boot the Context and init the Application.
         */
        $this->application = $environment->createApplication($context, $this->app);

        /**
         * This is where request and response are initialized.
         */
        if ($configurator) {
            $configurator($this->context);
        }

        /**
         * Init the Application.
         */
        try {
            $this->exception = null;
            $this->application->init();
            $this->application->run();
            /*
                        (new Request\Command\RunRequest($request))->execute(function () {
                            //ddd('ran request');
                        });
                        (new Response\Command\RunResponse($response, $request))->execute(function () {
                            //ddd('ran response');
                        });
            */
        } catch (Response\MockStop $e) {
            ddd('caught stop');
        } catch (\Throwable $e) {
            ddd('not caught', get_class($e));
            $this->exception = $e;
            error_log('EXCEPTION: ' . exception($e));
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
        return $this->getResponse()->getOutput();
    }

    /**
     * @return mixed|string|array|null
     * @throws \Exception
     */
    public function getDecodedOutput()
    {
        return json_decode($this->getOutput(), true);
    }
}

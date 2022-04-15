<?php

namespace Pckg\Framework\Test;

use Codeception\Test\Unit;
use Pckg\Concept\Context;
use Pckg\Framework\Application;
use Pckg\Framework\Environment;
use Pckg\Framework\Request;
use Pckg\Framework\Response;
use Throwable as Throwable;

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
     * @var Throwable
     */
    protected ?Throwable $exception = null;

    /**
     * @var string
     */
    protected $app;

    /**
     * MockRequest constructor.
     * @param Unit $test
     */
    public function __construct($test, $app)
    {
        $this->test = $test;
        $this->app = $app;
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function assertResponseCode($code, $message = null)
    {
        $response = $this->context->get(Response::class);

        $this->test->assertEquals($code, $response->getCode(), $message ?? ('Response code not ' . $code));

        return $this;
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function assertResponseHas($key)
    {
        $responseObject = $this->context->get(Response::class);
        $response = $responseObject->getOutput();

        $true = !!(is_array($response) ? ($response[$key] ?? null) : (json_decode($response, true)[$key] ?? null));
        $this->test->assertEquals(true, $true, 'Response does not have a/an ' . $key);

        return $this;
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function assertResponseContains($value)
    {
        $response = $this->context->get(Response::class)->getOutput();

        $this->test->assertEquals(true, str_contains($response, $value), 'Response does not contain ' . $value);

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
             * @var Request $request
             */
            $request = $context->get(Request::class);
            $request->server()->set('HTTP_X_REQUESTED_WITH', 'xmlhttprequest');
            $request->server()->set('HTTP_X_PCKG_CSRF', metaManager()->getCsrfValue());
            $request->server()->set('HTTP_REFERER', 'https://localhost');
            $request->server()->set('HTTP_ORIGIN', 'localhost:99999');
            $request->server()->set('HTTP_HOST', 'localhost');
            $this->mergeRequestHeaders($context, [
                'Accept' => 'application/json',
                'X-Pckg-CSRF' => metaManager()->getCsrfValue(),
            ]);
            if ($configurator) {
                $configurator($context);
            }
        };
    }

    /**
     * @param callable|null $configurator
     * @return $this
     */
    public function httpGet($url, callable $configurator = null, $mode = null)
    {
        return $this->fullHttpRequest($url, $this->modifyConfigurator($configurator, $mode), 'GET');
    }

    /**
     * @param callable|null $configurator
     * @return $this
     */
    protected function httpGetJson($url, callable $configurator = null)
    {
        return $this->fullHttpRequest($url, $this->modifyConfigurator($configurator, static::MODE_JSON), 'GET');
    }

    /**
     * @param callable|null $configurator
     * @return $this
     */
    protected function httpDelete($url, callable $configurator = null)
    {
        return $this->fullHttpRequest($url, $this->modifyConfigurator($configurator), 'DELETE');
    }

    /**
     * @param array $post
     * @param callable|null $configurator
     * @return $this
     */
    protected function httpPost($url, array $post = [], callable $configurator = null)
    {
        return $this->fullHttpRequest($url, function (Context $context) use ($post, $configurator) {
            $configurator = $this->modifyConfigurator($configurator, static::MODE_JSON);
            $configurator($context);
            $context->get(Request::class)->setPost($post);
        }, 'POST');
    }

    protected function initApp($url = '/', callable $configurator = null, $method = 'GET')
    {
        $context = $this->mockFramework($url, $method);
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

        try {
            $this->exception = null;
            $this->application->init();
            return $this;
        } catch (Throwable $e) {
            $this->exception = $e;
        }
    }

    /**
     * @param callable|null $configurator
     * @param string $method
     */
    protected function fullHttpRequest($url, callable $configurator = null, $method = 'GET')
    {
        $initialized = $this->initApp($url, $configurator, $method);
        if (!$initialized) {
            throw new \Exception('Cannot initialize app ' . ($this->exception ? exception($this->exception) : 'Unknown exception'));
        }

        /**
         * Init the Application.
         */
        try {
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
            //d('caught stop');
        } catch (Throwable $e) {
            //d('not caught', exception($e));
            $this->exception = $e;
            //error_log('MockRequest: EXCEPTION: ' . exception($e));
        }

        return $this;
    }

    /**
     * @return Context
     */
    protected function getContext()
    {
        return $this->context;
    }

    /**
     * @return Response
     * @throws \Exception
     */
    protected function getResponse()
    {
        return $this->getContext()->get(Response::class);
    }

    /**
     * @return mixed|string|array|null
     * @throws \Exception
     */
    protected function getOutput()
    {
        return $this->getResponse()->getOutput();
    }

    /**
     * @return mixed|string|array|null
     * @throws \Exception
     */
    protected function getDecodedOutput()
    {
        return json_decode($this->getOutput(), true);
    }
}

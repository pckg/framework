<?php

namespace Pckg\Framework;

use Pckg\Concept\Reflect;
use Pckg\Framework\Request\Data\Flash;
use Pckg\Framework\Response\Exceptions;
use Pckg\Framework\Router\URL;
use Pckg\Framework\View\AbstractView;
use Pckg\Framework\View\Twig;

class Response
{

    use Exceptions;

    protected $router;

    protected $environment;

    protected $output;

    protected $http = [
        100 => "HTTP/1.1 100 Continue",
        101 => "HTTP/1.1 101 Switching Protocols",
        200 => "HTTP/1.1 200 OK",
        201 => "HTTP/1.1 201 Created",
        202 => "HTTP/1.1 202 Accepted",
        203 => "HTTP/1.1 203 Non-Authoritative Information",
        204 => "HTTP/1.1 204 No Content",
        205 => "HTTP/1.1 205 Reset Content",
        206 => "HTTP/1.1 206 Partial Content",
        300 => "HTTP/1.1 300 Multiple Choices",
        301 => "HTTP/1.1 301 Moved Permanently",
        302 => "HTTP/1.1 302 Found",
        303 => "HTTP/1.1 303 See Other",
        304 => "HTTP/1.1 304 Not Modified",
        305 => "HTTP/1.1 305 Use Proxy",
        307 => "HTTP/1.1 307 Temporary Redirect",
        400 => "HTTP/1.1 400 Bad Request",
        401 => "HTTP/1.1 401 Unauthorized",
        402 => "HTTP/1.1 402 Payment Required",
        403 => "HTTP/1.1 403 Forbidden",
        404 => "HTTP/1.1 404 Not Found",
        405 => "HTTP/1.1 405 Method Not Allowed",
        406 => "HTTP/1.1 406 Not Acceptable",
        407 => "HTTP/1.1 407 Proxy Authentication Required",
        408 => "HTTP/1.1 408 Request Time-out",
        409 => "HTTP/1.1 409 Conflict",
        410 => "HTTP/1.1 410 Gone",
        411 => "HTTP/1.1 411 Length Required",
        412 => "HTTP/1.1 412 Precondition Failed",
        413 => "HTTP/1.1 413 Request Entity Too Large",
        414 => "HTTP/1.1 414 Request-URI Too Large",
        415 => "HTTP/1.1 415 Unsupported Media Type",
        416 => "HTTP/1.1 416 Requested range not satisfiable",
        417 => "HTTP/1.1 417 Expectation Failed",
        422 => "HTTP/1.1 422 Unprocessable entity",
        500 => "HTTP/1.1 500 Internal Server Error",
        501 => "HTTP/1.1 501 Not Implemented",
        502 => "HTTP/1.1 502 Bad Gateway",
        503 => "HTTP/1.1 503 Service Unavailable",
        504 => "HTTP/1.1 504 Gateway Time-out",
    ];

    protected $middlewares = [];

    protected $afterwares = [];

    public function __construct(Router $router, Environment $environment)
    {
        $this->router = $router;
        $this->environment = $environment;
    }

    public function addMiddleware($middleware)
    {
        $this->middlewares[] = $middleware;

        return $this;
    }

    public function getMiddlewares()
    {
        return $this->middlewares;
    }

    public function addAfterware($afterware)
    {
        $this->afterwares[] = $afterware;

        return $this;
    }

    public function getAfterwares()
    {
        return $this->afterwares;
    }

    public function code($code)
    {
        header($this->http[$code]);

        return $this;
    }

    public function init()
    {

    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function setOutput($output)
    {
        $this->output = $output;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function output()
    {
        echo $this->output;
    }

    public function run()
    {
        if ($this->output instanceof AbstractView || $this->output instanceof Twig) {
            if (request()->isAjax()) {
                $this->setOutput(
                    json_encode(array_merge($this->output->getData(), ['_html' => $this->output->autoparse()]))
                );
            } else {
                $this->setOutput($this->output->autoparse());
            }

        } else if (is_array($this->output)) {
            $this->setOutput(json_encode($this->output));

        } else if (is_object($this->output) && method_exists($this->output, '__toString')) {
            $this->setOutput((string)$this->output);

        }

        if (!$this->output) {
            $this->none();
        }

        $this->output();
    }

    private function getMinusUrl()
    {
        if (isset($_SERVER['HTTP_REFERER'])) {
            return $_SERVER['HTTP_REFERER'];
        }
    }

    public function redirect($url = null, $httpParams = [], $routerParams = [])
    {
        $output = null;
        if ($url === -1) {
            $url = $this->getMinusUrl();

            $output = '<html><body><script>history.go(-1);</script></body></html>';

        } else if (substr($url, 0, 1) == '@') {
            $url = (new URL())->setParams($httpParams)
                              ->setUrl(
                                  $this->router->make(
                                      substr($url, 1),
                                      $routerParams
                                  )
                              )->relative();

        } else if ($url === null) {
            $url = $this->router->getUri();

        }

        if (!$output) {
            $output = '<html><head><meta http-equiv="refresh" content="0; url=' . $url . '" /></head><body></body></html>';
        }

        /**
         * @T00D00 - implement event
         */
        trigger('response.redirect', [$this]);
        if (context()->exists(Flash::class)) {
            context()->get(Flash::class)->__destruct();
        }

        // try with php
        header("Location: " . $url);

        // fallback with html
        $this->setOutput($output);

        $this->output();
        exit;

        return $this;
    }

    public function respondWithSuccessRedirect($url = -1)
    {
        if ($url == -1) {
            $url = $this->getMinusUrl();
        }

        return request()->isAjax()
            ? $this->respondWithAjaxSuccessAndRedirect($url)
            : $this->redirect($url);
    }

    public function respondWithAjaxSuccess()
    {
        return $this->respond(
            [
                'success' => true,
                'error'   => false,
            ]
        );
    }

    public function respondWithAjaxError()
    {
        return $this->respond(
            [
                'success' => false,
                'error'   => true,
            ]
        );
    }

    public function respondWithAjaxSuccessAndRedirect($url)
    {
        if ($url == -1) {
            $url = $this->getMinusUrl();
        }

        return $this->respond(
            [
                'success'  => true,
                'error'    => false,
                'redirect' => $url,
            ]
        );
    }

    public function respondWithAjaxSuccessAndRedirectBack()
    {
        return $this->respond(
            [
                'success'  => true,
                'error'    => false,
                'redirect' => $this->getMinusUrl(),
            ]
        );
    }

    public function respond($string)
    {
        if (is_array($string)) {
            $string = json_encode($string);
        }

        echo $string;

        exit;

        return $this;
    }

}
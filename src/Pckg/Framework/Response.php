<?php

namespace Pckg\Framework;

use Pckg\Framework\Request\Data\Flash;
use Pckg\Framework\Request\Data\Session;
use Pckg\Framework\Response\Exceptions;
use Pckg\Framework\Router\URL;
use Throwable;

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

    protected $code = 200;

    protected $responded = false;

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
        $this->code = $code;
        header($this->http[$code]);

        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param $output
     *
     * @return $this
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    public function getOutput()
    {
        return $this->output;
    }

    private function getMinusUrl()
    {
        if (isset($_SERVER['HTTP_REFERER'])) {
            return $_SERVER['HTTP_REFERER'];
        }
    }

    public function hasResponded()
    {
        return $this->responded;
    }

    public function internal($url)
    {
        try {
            /**
             * Circular redirect.
             */
            if ($_SERVER['REQUEST_URI'] == $url) {
                $this->redirect($url);
            }

            /**
             * Set GET method.
             */
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['REQUEST_URI'] = $url;
            $_POST = [];

            $oldContext = context();
            $context = \Pckg\Framework\Helper\Context::createInstance();

            /**
             * Circular redirects.
             */
            if (count(\Pckg\Framework\Helper\Context::getInstances()) > 4) {
                $this->redirect($url);
            }

            $context->boot(get_class($oldContext->get(Environment::class)));

            exit;
        } catch (Throwable $e) {
            if (!prod()) {
                echo exception($e);
            }
        }

        exit;

        return $this;
    }

    public function redirect($url = null, $routerParams = [], $httpParams = [])
    {
        $output = null;
        if ($url === -1) {
            $url = $this->getMinusUrl();

            $output = '<html><body><script>history.go(-1);</script></body></html>';
        } elseif (substr($url, 0, 1) == '@') {
            $url = (new URL())->setParams($httpParams)->setUrl($this->router->make(substr($url, 1),
                                                                                   $routerParams))->relative();
        } elseif ($url === null) {
            $url = $this->router->getUri();
        }

        if (!$output) {
            if (request()->isJson() || request()->isAjax()) {
                $output = json_encode(['output' => null]);
            } else {
                $output = '<html><head><meta http-equiv="refresh" content="0; url=' . $url . '" /></head><body></body></html>';
            }
        }

        /**
         * @T00D00 - implement event
         */
        trigger(Response::class . '.redirect', [$this]);
        if ($flash = context()->getOrDefault(Flash::class)) {
            $flash->__destruct();
        }
        if ($session = context()->getOrDefault(Session::class)) {
            $session->__destruct();
        }

        $code = $this->getCode();
        if ($code == 200) {
            $this->code(301);
        }

        // try with php
        header("Location: " . $url);

        // fallback with html
        $this->respond($output);
        exit;

        return $this;
    }

    /**
     * @param array $response
     */
    public function unprocessable($response = [])
    {
        if (is_array($response) && !array_key_exists('success', $response)) {
            $response['success'] = false;
        }

        $this->code(422);
        $this->respond($response);
    }

    /**
     * @T00D00 - rename this to success()
     * @return Response
     */
    public function respondWithSuccess($data = [])
    {
        $this->code = 200;

        return request()->isJson() || request()->isAjax() ? $this->respondWithAjaxSuccess($data) : $this->redirect();
    }

    public function respondWithSuccessRedirect($url = -1)
    {
        if ($url == -1) {
            $url = $this->getMinusUrl();
        }

        $this->code = 200;

        return request()->isJson() || request()->isAjax() ? $this->respondWithAjaxSuccessAndRedirect($url) : $this->redirect($url);
    }

    /**
     * @T00D00 - rename this to ajaxSuccess()
     * @return Response
     */
    public function respondWithAjaxSuccess($data = [])
    {
        $this->code = 200;

        return $this->respond(array_merge([
                                              'success' => true,
                                              'error'   => false,
                                          ],
                                          $data));
    }

    public function respondWithAjaxSuccessAndRedirect($url)
    {
        if ($url == -1) {
            $url = $this->getMinusUrl();
        }

        $this->code = 200;

        return $this->respond([
                                  'success'  => true,
                                  'error'    => false,
                                  'redirect' => $url,
                              ]);
    }

    public function respondWithAjaxSuccessAndRedirectBack()
    {
        $this->code = 200;

        return $this->respond([
                                  'success'  => true,
                                  'error'    => false,
                                  'redirect' => $this->getMinusUrl(),
                              ]);
    }

    public function respondWithSuccessOrRedirect($url)
    {
        $this->code = 200;

        return request()->isJson() || request()->isAjax() ? $this->respondWithAjaxSuccessAndRedirect($url) : $this->redirect($url);
    }

    public function respondWithSuccessOrRedirectBack()
    {
        $this->code = 200;

        return $this->respondWithSuccessOrRedirect(-1);
    }

    public function respondWithError($data = [])
    {
        return request()->isJson() || request()->isAjax() ? $this->respondWithAjaxError($data) : $this->notFound();
    }

    public function respondWithErrorRedirect($url = -1)
    {
        if ($url == -1) {
            $url = $this->getMinusUrl();
        }

        $this->code = 200;

        return request()->isAjax() ? $this->respondWithAjaxErrorAndRedirect($url) : $this->redirect($url);
    }

    public function respondWithAjaxError($data = [])
    {
        $this->code = 200;

        return $this->respond(array_merge([
                                              'success' => false,
                                              'error'   => true,
                                          ],
                                          $data));
    }

    public function respondWithAjaxErrorAndRedirect($url)
    {
        if ($url == -1) {
            $url = $this->getMinusUrl();
        }

        $this->code = 200;

        return $this->respond([
                                  'success'  => false,
                                  'error'    => true,
                                  'redirect' => $url,
                              ]);
    }

    /**
     * @param $array array
     *
     * @return string
     */
    public function arrayToString(array $array)
    {
        $this->sendJsonHeader();

        return json_encode((object)$array, JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    public function respondAndContinue($string = null, $seconds = 120)
    {
        if ($this->responded) {
            return;
        }
        
        $isJson = false;
        if (is_array($string)) {
            $isJson;
            $string = $this->arrayToString($string);
        }

        ignore_user_abort(true);
        set_time_limit($seconds);

        ob_start();
        echo $string;
        if ($isJson) {
            $this->sendJsonHeader();
        }
        $this->code(202);
        header("Content-Length: " . ob_get_length());
        header("Connection: close");
        ob_end_flush();
        ob_flush();
        flush();

        $this->responded = true;

        if (session_id()) {
            session_write_close();
        }
    }

    public function stop($code = 0)
    {
        trigger(Response::class . '.responded');

        exit($code);
    }

    public function respond($string = null)
    {
        if (is_array($string)) {
            $string = $this->arrayToString($string);
        } else if (!$string && func_get_args()) {
            $string = $this->output;
        }

        trigger(Response::class . '.responding');

        $this->code($this->code);

        echo $string;

        $this->stop();

        return $this;
    }

    public function image($file)
    {
        $this->sendFileContentTypeHeaders($file);
        $this->sendContentLengthHeader($file);
        $this->readFile($file);
    }

    /**
     * @param $file
     *
     * @return $this
     */
    public function sendContentLengthHeader($file)
    {
        header("Content-Length: " . filesize($file));

        return $this;
    }

    /**
     * Read file with file_get_contents or handle (> 10MB) and print contents.
     *
     * @param $file
     */
    public function readFile($file)
    {
        /**
         * Set limit.
         */
        $limit = 1024 * 1024 * 10;

        /**
         * Read with handle and exit.
         */
        if (filesize($file) > $limit) {
            $fp = fopen($file, "r");
            while (!feof($fp)) {
                echo fread($fp, $limit);
                flush();
            }

            exit;
        }

        /**
         * Read whole file and exit.
         */
        echo file_get_contents($file);
        exit;
    }

    public function download($file, $filename)
    {
        $this->sendFileContentTypeHeaders($filename);
        $this->sendFileDispositionHeader($filename);
        $this->sendContentLengthHeader($file);
        $this->readFile($file);
    }

    public function printFile($file, $filename)
    {
        $this->sendFileContentTypeHeaders($filename);
        $this->sendContentLengthHeader($file);
        $this->readFile($file);
    }

    public function downloadString($string, $filename)
    {
        $this->sendFileContentTypeHeaders($filename);
        $this->sendFileDispositionHeader($filename);
        header("Content-Length: " . mb_strlen($string));
        echo $string;
        exit;
    }

    public function sendFileContentTypeHeaders($filename = null)
    {
        if (strpos($filename, '.pdf')) {
            header("Content-Type: application/pdf");
        } elseif (strpos($filename, '.png')) {
            header("Content-Type: image/png");
        } else {
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
        }
    }

    public function sendJsonHeader()
    {
        header("Content-Type: application/json");
    }

    public function sendFileDispositionHeader($filename)
    {
        header("Content-Disposition: attachment; filename=" . $filename);
        header("Content-Description: File Transfer");
    }

    public function sendCacheHeaders($seconds = 60)
    {
        $timestamp = gmdate("D, d M Y H:i:s", time() + $seconds) . " GMT";
        header("Expires: " . $timestamp);
        header("Pragma: cache");
        header("Cache-Control: max-age=" . $seconds);
    }

    public function sendNoCacheHeaders()
    {
        $timestamp = gmdate("D, d M Y H:i:s") . " GMT";
        header("Expires: " . $timestamp);
        header("Last-Modified: " . $timestamp);
        header("Pragma: no-cache");
        header("Cache-Control: no-cache, must-revalidate");
    }

    public function sendFeaturePolicyHeader()
    {
        header("Feature-Policy: usb 'self'");
    }

}
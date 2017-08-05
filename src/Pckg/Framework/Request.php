<?php

namespace Pckg\Framework;

use Pckg\Framework\Helper\Lazy;

class Request extends Lazy
{

    const GET = 'GET';

    const POST = 'POST';

    const PUT = 'PUT';

    const DELETE = 'DELETE';

    protected $url;

    public $post, $get, $server, $session, $cookie, $files;

    protected $router, $response;

    protected $match;

    protected $internal = 0;

    protected $internals = [];

    function __construct(Router $router, Response $response)
    {
        $this->router = $router;
        $this->response = $response;

        $input = file_get_contents('php://input');
        if ($this->isJson()) {
            $input = json_decode($input, true);
        } else {
            parse_str($input, $input);
        }

        if (!$input && $_POST) {
            /**
             * Why is php input sometimes empty.
             */
            $input = $_POST;
        }

        $this->post = new Lazy($input);
        $this->get = new Lazy($_GET);
        $this->server = new Lazy($_SERVER);
        $this->files = new Lazy($_FILES);
        $this->cookie = new Lazy($_COOKIE);
        $this->request = new Lazy($_REQUEST);

        $this->fetchUrl();
    }

    public function setInternal()
    {
        $this->internal++;
        $this->internals[] = $this->url;

        if ($this->internal > 2) {
            die($this->internal . ' internal redirects');
        }
    }

    public function fetchUrl()
    {
        $parsedUrl = parse_url($_SERVER['REQUEST_URI'] ?? '/');

        $url = $parsedUrl['path'];

        $envPrefix = env()->getUrlPrefix();

        // replace environment prefix
        if (strpos($url, $envPrefix) === 0) {
            $url = substr($url, strlen($envPrefix));
        }

        // default url if empty
        if (!$url) {
            $url = '/';
        } else if (strlen($url) > 1 && substr($url, -1) == "/") {
            // add / to beginning
            $url = substr($url, 0, -1);
        }

        $this->setUrl($url);
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function setMatch($match)
    {
        $this->match = $match;

        return $this;
    }

    public function getMatch()
    {
        return $this->match;
    }

    public function method()
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    function post($key = null, $default = [])
    {
        if (is_array($key)) {
            $return = [];
            foreach ($key as $k => $v) {
                $return[is_int($k) ? $v : $k] = $this->post->get($v);
            }

            return $return;
        } elseif (!$key) {
            return $this->post;
        }

        return $this->post->get($key, $default);
    }

    function get($key = null, $default = [])
    {
        return is_null($key)
            ? $this->get
            : $this->get->get($key, $default);
    }

    function server($key = null, $default = [])
    {
        return is_null($key)
            ? $this->server
            : $this->server->get($key, $default);
    }

    function session($key = null, $default = [])
    {
        return is_null($key)
            ? $this->session
            : $this->session->get($key, $default);
    }

    function request($key = null, $default = [])
    {
        return is_null($key)
            ? $this->request
            : $this->request->get($key, $default);
    }

    function files($key = null)
    {
        return is_null($key)
            ? $this->files
            : $this->files->get($key);
    }

    function isMethod($method)
    {
        return $this->method() == strtoupper($method);
    }

    /**
     * @return string
     * @deprecated
     * @see method()
     */
    public function getMethod()
    {
        return $this->method();
    }

    public function isJson()
    {
        $headers = getallheaders();

        return ($headers['content-type'] ?? null) == 'application/json';
    }

    function isAjax()
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower(
                                                                 $_SERVER['HTTP_X_REQUESTED_WITH']
                                                             ) == 'xmlhttprequest') || isset($_POST['ajax']);
    }

    function isPost()
    {
        return $this->isMethod(self::POST);
    }

    function isGet()
    {
        return $this->isMethod(self::GET);
    }

    function host()
    {
        return $_SERVER['HTTP_HOST'];
    }

    function scheme()
    {
        return $_SERVER['REQUEST_SCHEME'];
    }

    function url()
    {
        return $this->url;
    }

    function getUrl()
    {
        return $this->url;
    }
}

?>
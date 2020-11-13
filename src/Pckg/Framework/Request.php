<?php

namespace Pckg\Framework;

use Pckg\Concept\Reflect;
use Pckg\Framework\Helper\Lazy;
use Pckg\Framework\Request\Data\Cookie;
use Pckg\Framework\Request\Data\Server;
use Pckg\Framework\Request\Message;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class Request
 * @package Pckg\Framework
 * PSR7 implementation of Request.
 */
class Request extends Message implements RequestInterface, ServerRequestInterface
{

    const OPTIONS = 'OPTIONS';

    const GET = 'GET';

    const POST = 'POST';

    const PUT = 'PUT';

    const PATCH = 'PATCH';

    const SEARCH = 'SEARCH';

    const DELETE = 'DELETE';

    const HEAD = 'HEAD';

    protected $router, $response;

    protected $match;

    protected $internal = 0;

    protected $internals = [];

    protected $attributes = [];

    /**
     * @var UriInterface
     */
    protected $uri;

    function __construct($input = [])
    {
        Reflect::method($this, 'initDependencies');

        if (!$input) {
            $input = file_get_contents('php://input');
            if ($input && (strpos($input, '{') === 0 && strrpos($input, '}') === strlen($input) - 1)) {
                $input = json_decode($input, true);
            } elseif ($input) {
                parse_str($input, $input);
            } else {
                $input = [];
            }
        }

        if (!$input && $_POST) {
            /**
             * Why is php input sometimes empty?
             */
            $input = $_POST;
        }

        $this->setPost($input);
        $this->get = new Lazy($_GET);
        $this->server = new Server($_SERVER);
        $this->files = new Lazy($_FILES);
        $this->cookie = new Cookie($_COOKIE);
        $this->request = new Lazy($_REQUEST);

        $this->fetchUrl();

        $this->headers = collect(getallheaders())->groupBy(function ($value, $key) {
            return $key;
        })->all();
    }

    public function setConstructs($post, $get, $server, $files, $cookie, $request, $headers = [])
    {
        $this->setPost($post);
        $this->get = new Lazy($get);
        $this->server = new Lazy($server);
        $this->files = new Lazy($files);
        $this->cookie = new Cookie($cookie);
        $this->request = new Lazy($request);
        $this->headers = $headers;

        $this->fetchUrl();

        return $this;
    }

    /**
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers = [])
    {
        $this->headers = $headers;

        return $this;
    }

    public function setPost(array $post = [])
    {
        $this->post = new Lazy($post);

        return $this;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function initDependencies(Router $router, Response $response)
    {
        $this->router = $router;
        $this->response = $response;
    }

    public function setInternal()
    {
        $this->internal++;
        $this->internals[] = $this->url;

        if ($this->internal > 2) {
            die($this->internal . ' internal redirects');
        }
    }

    public function setMatch($match)
    {
        $this->match = $match;

        return $this;
    }

    public function getMatch($key = null)
    {
        return $key
            ? ($this->match[$key] ?? null)
            : $this->match;
    }

    /**
     * @return mixed|Lazy|null
     */
    public function method()
    {
        return $this->server('REQUEST_METHOD', 'GET');
    }

    /**
     * @param null $key
     * @param array $default
     * @return Post|mixed|array|string|null
     */
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

    function cookie($key = null, $default = [])
    {
        return is_null($key)
            ? $this->cookie
            : $this->cookie->get($key, $default);
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
        $contentType = $this->header('Content-Type');
        $accept = $this->header('Accept');

        return in_array($contentType, ['application/json', 'application/x-www-form-urlencoded'])
               || strpos($accept, 'application/json') !== false;
    }

    public function header($key)
    {
        return $this->getHeaders()[$key][0] ?? null;
    }

    public function clientIp()
    {
        return first(server('HTTP_X_FORWARDED_FOR'), server('REMOTE_ADDR'));
    }

    public function clientPort()
    {
        return first(server('HTTP_X_FORWARDED_PORT'), server('SERVER_PORT'));
    }

    function isAjax()
    {
        return strtolower($this->server('HTTP_X_REQUESTED_WITH', null)) === 'xmlhttprequest' || isset($_POST['ajax']);
    }

    function isOptions()
    {
        return $this->isMethod(self::OPTIONS);
    }

    function isGet()
    {
        return $this->isMethod(self::GET);
    }

    function isPost()
    {
        return $this->isMethod(self::POST);
    }

    function isPut()
    {
        return $this->isMethod(self::PUT);
    }

    function isPatch()
    {
        return $this->isMethod(self::PATCH);
    }

    function isSearch()
    {
        return $this->isMethod(self::SEARCH);
    }

    function isDelete()
    {
        return $this->isMethod(self::DELETE);
    }

    function isHead()
    {
        return $this->isMethod(self::HEAD);
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

    /**
     * @return bool
     *
     * Check if request was made by bot.
     */
    public function isBot()
    {
        /**
         * Return false immediately for non-http requests.
         */
        if (!isHttp()) {
            return false;
        }

        /**
         * Return true for bots madched by user agent.
         */
        if (preg_match('/apple|baidu|bingbot|facebookexternalhit|googlebot|-google|ia_archiver|msnbot|naverbot|pingdom|seznambot|slurp|teoma|twitter|yandex|yeti/bot|crawl|curl|dataprovider|search|get|spider|find|java|majesticsEO|google|yahoo|teoma|contaxe|yandex|libwww-perl|facebookexternalhit/i',
                       $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }

        /**
         * Return false by default.
         */
        return false;
    }

    public function isSecure()
    {
        return server('HTTPS') ? true : false;
    }

    public function getDomain()
    {
        return first(server('HTTP_HOST', null), server('SERVER_NAME', null));
    }

    /**
     * @return bool
     */
    public function isCORS()
    {
        /**
         * Leave OPTIONS and GET?
         */
        return $this->isPost() || $this->isSearch() || $this->isDelete() || $this->isPut() || $this->isPatch() || $this->isHead();
    }

    /**
     * @return string
     */
    public function getRequestTarget()
    {
        return $this->getUrl();
    }

    /**
     * @param mixed $requestTarget
     * @return $this|Request
     */
    public function withRequestTarget($requestTarget)
    {
        $this->setUrl($requestTarget);

        return $this;
    }

    /**
     * @param string $method
     * @return $this|Request
     */
    public function withMethod($method)
    {
        $this->server->set('REQUEST_METHOD', $method);
        
        return $this;
    }

    /**
     * @return UriInterface
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param UriInterface $uri
     * @param false $preserveHost
     * @return $this|Request
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @return array|void
     */
    public function getServerParams()
    {
        return $this->server->all();
    }

    /**
     * @return array|mixed
     */
    public function getCookieParams()
    {
        return $this->cookie->all();
    }

    /**
     * @param array $cookies
     * @return $this|Request
     */
    public function withCookieParams(array $cookies)
    {
        $this->cookie->setData($cookies);

        return $this;
    }

    /**
     * @return array|mixed
     */
    public function getQueryParams()
    {
        return $this->get->all();
    }

    /**
     * @param array $query
     * @return $this|Request
     */
    public function withQueryParams(array $query)
    {
        $this->get->setData($query);

        return $this;
    }

    /**
     * @return array|void
     */
    public function getUploadedFiles()
    {
        return $this->files->all();
    }

    /**
     * @param array $uploadedFiles
     * @return $this|Request
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $this->files->setData($uploadedFiles);

        return $this;
    }

    /**
     * @return array|object|void|null
     */
    public function getParsedBody()
    {
        return $this->post->all();
    }

    /**
     * @param array|object|null $data
     * @return $this|Request
     */
    public function withParsedBody($data)
    {
        $this->body = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return [];
    }

    /**
     * @param string $name
     * @param null $default
     * @return $this|mixed
     */
    public function getAttribute($name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this|Request
     */
    public function withAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @return $this|Request
     */
    public function withoutAttribute($name)
    {
        unset($this->attributes[$name]);

        return $this;
    }


}

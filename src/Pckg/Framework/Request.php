<?php

namespace Pckg\Framework;

use Pckg\Concept\Reflect;
use Pckg\Framework\Helper\Lazy;
use Pckg\Framework\Request\Data\Cookie;
use Pckg\Framework\Request\Data\Files;
use Pckg\Framework\Request\Data\Get;
use Pckg\Framework\Request\Data\Post;
use Pckg\Framework\Request\Data\Server;
use Pckg\Framework\Request\Data\Session;
use Pckg\Framework\Request\Data\Request as DataRequest;
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

    protected $match;

    protected $internal = 0;

    protected $internals = [];

    protected $attributes = [];

    /**
     * @var UriInterface
     */
    protected $uri;

    public function __construct()
    {
        $this->server = (new Server())->setFromGlobals();
        $this->request = (new DataRequest())->setFromGlobals();
        $this->post = (new Post())->setFromGlobals();
        $this->get = (new Get())->setFromGlobals();
        $this->cookie = (new Cookie())->setFromGlobals();
        $this->files = (new Files())->setFromGlobals();
        $this->headers = collect(getallheaders())->groupBy(function ($value, $key) {
            return $key;
        })->all();

        $this->fetchUrl();
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
        $this->post = new Post($post);

        return $this;
    }

    public function getHeaders()
    {
        return $this->headers;
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
    public function post($key = null, $default = [])
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

    /**
     * @param null $key
     * @param array $default
     * @return Lazy|mixed
     */
    private function getOrFull($object, $key = null, $default = [])
    {
        return is_null($key)
            ? $object
            : $object->get($key, $default);
    }

    /**
     * @param null $key
     * @param array $default
     * @return Get|mixed
     */
    public function get($key = null, $default = [])
    {
        return $this->getOrFull($this->get, $key, $default);
    }

    /**
     * @param null $key
     * @param array $default
     * @return Server|mixed
     */
    public function server($key = null, $default = [])
    {
        return $this->getOrFull($this->server, $key, $default);
    }

    /**
     * @param null $key
     * @param array $default
     * @return Cookie|mixed
     */
    public function cookie($key = null, $default = [])
    {
        return $this->getOrFull($this->cookie, $key, $default);
    }

    /**
     * @param null $key
     * @param array $default
     * @return Session|mixed
     */
    public function session($key = null, $default = [])
    {
        return $this->getOrFull($this->session, $key, $default);
    }

    /**
     * @param null $key
     * @param array $default
     * @return \Pckg\Htmlbuilder\Datasource\Method\Request|mixed
     */
    public function request($key = null, $default = [])
    {
        return $this->getOrFull($this->request, $key, $default);
    }

    /**
     * @param null $key
     * @param array $default
     * @return Lazy|mixed
     */
    public function files($key = null, $default = [])
    {
        return $this->getOrFull($this->files, $key, $default);
    }

    public function isMethod($method)
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

    public function isAjax()
    {
        return strtolower($this->server('HTTP_X_REQUESTED_WITH', null)) === 'xmlhttprequest' || isset($_POST['ajax']);
    }

    public function isOptions()
    {
        return $this->isMethod(self::OPTIONS);
    }

    public function isGet()
    {
        return $this->isMethod(self::GET);
    }

    public function isPost()
    {
        return $this->isMethod(self::POST);
    }

    public function isPut()
    {
        return $this->isMethod(self::PUT);
    }

    public function isPatch()
    {
        return $this->isMethod(self::PATCH);
    }

    public function isSearch()
    {
        return $this->isMethod(self::SEARCH);
    }

    public function isDelete()
    {
        return $this->isMethod(self::DELETE);
    }

    public function isHead()
    {
        return $this->isMethod(self::HEAD);
    }

    public function host()
    {
        return $_SERVER['HTTP_HOST'];
    }

    public function scheme()
    {
        return $_SERVER['REQUEST_SCHEME'];
    }

    public function url()
    {
        return $this->url;
    }

    public function getUrl($stripParams = false)
    {
        $url = $this->url;

        if ($stripParams) {
            [$url] = explode('?', $url);
        }

        return $url;
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
        if (
            preg_match(
                '/apple|baidu|bingbot|facebookexternalhit|googlebot|-google|ia_archiver|msnbot|naverbot|pingdom|seznambot|slurp|teoma|twitter|yandex|yeti\/bot|crawl|curl|dataprovider|search|get|spider|find|java|majesticsEO|google|yahoo|teoma|contaxe|yandex|libwww-perl/i',
                $_SERVER['HTTP_USER_AGENT']
            )
        ) {
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
        return !($this->isGet() || $this->isOptions());
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
        return $this->post()->all();
    }

    /**
     * @param string $name
     * @param null $default
     * @return $this|mixed
     */
    public function getAttribute($name, $default = null)
    {
        return $this->post()->get($name, $default);
        return $this->attributes[$name] ?? $default;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this|Request
     */
    public function withAttribute($name, $value)
    {
        $this->post()->set($name, $value);
        // $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @return $this|Request
     */
    public function withoutAttribute($name)
    {
        unset($this->post()[$name]);
        //unset($this->attributes[$name]);

        return $this;
    }

    public function mock(callable $task)
    {
        return context()->mock(new Request(), $task);
    }
}

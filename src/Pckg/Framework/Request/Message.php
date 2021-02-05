<?php

namespace Pckg\Framework\Request;

use GuzzleHttp\Psr7\BufferStream;
use GuzzleHttp\Psr7\Stream;
use Pckg\Concept\Reflect;
use Pckg\Framework\Helper\Lazy;
use Pckg\Framework\Request\Data\Cookie;
use Pckg\Framework\Request\Data\Server;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class Message
 * @package pckg\Framework\Request
 * PSR7 implementation of Message.
 */
class Message extends Lazy implements MessageInterface
{

    /**
     * @var string
     */
    protected $url;

    /**
     * @var Lazy|Cookie
     */
    protected $post, $get, $server, $session, $cookie, $files;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var StreamInterface
     */
    protected $body;

    function __construct($input = [])
    {
        $this->post = new Lazy([]);
        $this->get = new Lazy([]);
        $this->server = new Server([]);
        $this->files = new Lazy([]);
        $this->cookie = new Cookie([]);
        $this->request = new Lazy([]);

        $this->url = '/';

        $this->fetchUrl();
        $this->body = new BufferStream();
    }

    /**
     *
     */
    public function fetchUrl()
    {
        $parsedUrl = parse_url($this->server->get('REQUEST_URI', '/') ?? '/');

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

    /**
     * @return mixed|string|string[]
     */
    public function getProtocolVersion()
    {
        $version = str_replace('HTTP/', '', $this->server->get('SERVER_PROTOCOL'));
        return $version ?? '1.1';
    }

    /**
     * @param string $version
     * @return $this|Message
     */
    public function withProtocolVersion($version)
    {
        $this->server->set('PROTOCOL_VERSION', 'HTTP/' . $version);

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasHeader($name)
    {
        return collect($this->headers)->has(function ($value, $key) use ($name) {
            return strtolower($key) === strtolower($name);
        });
    }

    /**
     * @param string $name
     * @return array|string[]
     */
    public function getHeader($name)
    {
        return collect($this->headers)->filter(function ($value, $key) use ($name) {
            return strtolower($key) === strtolower($name);
        })->first();
    }

    /**
     * @param string $name
     * @return string
     */
    public function getHeaderLine($name)
    {
        return collect($this->getHeader($name))->implode(',') ?? '';
    }

    /**
     * @param string $name
     * @param string|string[] $value
     * @return Message|void
     */
    public function withHeader($name, $value)
    {
        $headers = collect($this->headers)->filter(function ($value, $key) use ($name) {
            return strtolower($key) !== strtolower($name);
        })->all();

        $headers[$name] = $value;
        $this->header = $headers;

        return $this;
    }

    /**
     * @param string $name
     * @param string|string[] $value
     * @return Message|void
     */
    public function withAddedHeader($name, $value)
    {
        if (!array_key_exists($key, $this->headers)) {
            $this->headers[$key] = [];
        }

        $this->headers[$key][] = $value;
        return $this->headers[$key];
    }

    /**
     * @param string $name
     * @return Message|void
     */
    public function withoutHeader($name)
    {
        $this->headers = collect($this->headers)->filter(function ($value, $key) use ($name) {
            return strtolower($name) !== strtolower($key);
        })->all();

        return $this;
    }

    /**
     * @return StreamInterface|void
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param StreamInterface $body
     * @return Message|void
     */
    public function withBody(StreamInterface $body)
    {
        $this->body = $body;

        return $this;
    }
}

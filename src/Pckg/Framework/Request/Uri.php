<?php

namespace Pckg\Framework\Request;

use Psr\Http\Message\UriInterface;

/**
 * Class Uri
 * @package Pckg\Framework\Request
 * PSR7 implementation of Uri.
 */
class Uri implements UriInterface
{

    /**
     * @var string
     */
    protected $scheme = '';

    /**
     * @var string
     */
    protected $user = '';

    /**
     * @var string
     */
    protected $pass = '';

    /**
     * @var
     */
    protected $host = '';

    /**
     * @var int|null
     */
    protected $port = null;

    /**
     * @var string
     */
    protected $path = '/';

    /**
     * @var string
     */
    protected $query = '';

    /**
     * @var string
     */
    protected $fragment = '';

    /**
     * Uri constructor.
     * @param $uri
     */
    public function __construct($uri)
    {
        $parsed = parse_url($url);
        $keys = [
            'host', 'port', 'user', /*'pass', */
            'path', 'query', 'fragment',
        ];
        foreach ($parsed as $key => $val) {
            if (!in_array($key, $keys)) {
                continue;
            } else if ($key === 'user') {
                $this->withUserInfo($val, $parsed['pass'] ?? null);
                continue;
            }
            $this->{'with' . ucfirst($key)}($val);
        }
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @return string
     */
    public function getAuthority()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getUserInfo()
    {
        return '';
    }

    /**
     * @return string|void
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int|void|null
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * @param string $scheme
     * @return $this|Uri
     */
    public function withScheme($scheme)
    {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * @param string $user
     * @param null $password
     * @return $this|Uri
     */
    public function withUserInfo($user, $password = null)
    {
        $this->user = $user;
        $this->pass = $password:

        return $this;
    }

    /**
     * @param string $host
     * @return $this|Uri
     */
    public function withHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @param int|null $port
     * @return $this|Uri
     */
    public function withPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @param string $path
     * @return $this|Uri
     */
    public function withPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @param string $query
     * @return $this|Uri
     */
    public function withQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @param string $fragment
     * @return $this|Uri
     */
    public function withFragment($fragment)
    {
        $this->fragment = $fragment;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->scheme . '://' . appendIf($this->user . prependIf(':', $this->pass), '@')
            . $this->host . prependIf(':', $this->port) . $this->path
            . prependIf('?' . $this->query) . prependIf('#', $this->fragment);
    }
}

<?php namespace Pckg\Framework\Helper;

use Pckg\Auth\Service\Auth;
use Pckg\Framework\Request;
use Pckg\Framework\Request\Data\Cookie;
use Pckg\Framework\Request\Data\Get;
use Pckg\Framework\Request\Data\Post;
use Pckg\Framework\Request\Data\Server;
use Pckg\Framework\Request\Data\Session;
use Pckg\Framework\Response;
use Pckg\Framework\Router;
use Pckg\Manager\Asset;
use Pckg\Manager\Locale;
use Pckg\Manager\Seo;
use Pckg\Manager\Vue;

trait Traits
{

    /**
     * @var Response
     */
    private $response;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var Post
     */
    private $post;

    /**
     * @var Get
     */
    private $get;

    /**
     * @var Cookie
     */
    private $cookie;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var Server
     */
    private $server;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var Asset
     */
    private $assetManager;

    /**
     * @var Seo
     */
    private $seoManager;

    /**
     * @var Vue
     */
    private $vueManager;

    /**
     * @var Locale
     */
    private $localeManager;

    /**
     * @return Response
     */
    public function response()
    {
        if (!$this->response) {
            $this->response = resolve(Response::class);
        }

        return $this->response;
    }

    /**
     * @return Request
     */
    public function request()
    {
        if (!$this->request) {
            $this->request = resolve(Request::class);
        }

        return $this->request;
    }

    /**
     * @return Post|string|array
     */
    public function post($key = null, $default = null)
    {
        if (!$this->post) {
            $this->post = resolve(Post::class);
        }

        if ($key) {
            return $this->post->get($key, $default);
        }

        return $this->post;
    }

    /**
     * @return Get
     */
    public function get($key = null, $default = null)
    {
        if (!$this->get) {
            $this->get = resolve(Get::class);
        }

        if ($key) {
            return $this->get->get($key, $default);
        }

        return $this->get;
    }

    /**
     * @return Post
     */
    public function session()
    {
        if (!$this->session) {
            $this->session = resolve(Session::class);
        }

        return $this->session;
    }

    public function server($key = null, $default = null)
    {
        if (!$this->server) {
            $this->server = resolve(Server::class);
        }

        if ($key) {
            return $this->server->get($key, $default);
        }

        return $this->server;
    }

    public function cookie($key = null, $default = null)
    {
        if (!$this->cookie) {
            $this->cookie = resolve(Cookie::class);
        }

        if ($key) {
            return $this->cookie->get($key, $default);
        }

        return $this->cookie;
    }

    public function auth($provider = null)
    {
        if (!$this->auth) {
            $this->auth = resolve(Auth::class);
        }

        if ($provider) {
            $this->auth->useProvider($provider);
        }

        return $this->auth;
    }

    public function router()
    {
        if (!$this->router) {
            $this->router = resolve(Router::class);
        }

        return $this->router;
    }

    public function assetManager()
    {
        if (!$this->assetManager) {
            $this->assetManager = resolve(Asset::class);
        }

        return $this->assetManager;
    }

    /**
     * @return Seo
     */
    public function seoManager()
    {
        if (!$this->seoManager) {
            $this->seoManager = resolve(Seo::class);
        }

        return $this->seoManager;
    }

    /**
     * @return Vue
     */
    public function vueManager()
    {
        if (!$this->vueManager) {
            $this->vueManager = resolve(Vue::class);
        }

        return $this->vueManager;
    }

    /**
     * @return Locale
     */
    public function localeManager()
    {
        if (!$this->localeManager) {
            $this->localeManager = resolve(Locale::class);
        }

        return $this->localeManager;
    }

}
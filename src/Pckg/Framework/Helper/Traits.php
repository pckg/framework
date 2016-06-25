<?php namespace Pckg\Framework\Helper;

use Pckg\Auth\Service\Auth;
use Pckg\Framework\Request;
use Pckg\Framework\Request\Data\Get;
use Pckg\Framework\Request\Data\Post;
use Pckg\Framework\Request\Data\Session;
use Pckg\Framework\Response;
use Pckg\Framework\Router;
use Pckg\Manager\Asset;
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
     * @var Session
     */
    private $session;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var Asset
     */
    private $assetManager;

    /**
     * @var Vue
     */
    private $vueManager;

    public function response() {
        if (!$this->response) {
            $this->response = resolve(Response::class);
        }

        return $this->response;
    }

    /**
     * @return Request
     */
    public function request() {
        if (!$this->request) {
            $this->request = resolve(Request::class);
        }

        return $this->request;
    }

    /**
     * @return Post
     */
    public function post() {
        if (!$this->post) {
            $this->post = resolve(Post::class);
        }

        return $this->post;
    }

    /**
     * @return Get
     */
    public function get() {
        if (!$this->get) {
            $this->get = resolve(Get::class);
        }

        return $this->get;
    }

    /**
     * @return Post
     */
    public function session() {
        if (!$this->session) {
            $this->session = resolve(Session::class);
        }

        return $this->session;
    }

    public function auth() {
        if (!$this->auth) {
            $this->auth = resolve(Auth::class);
        }

        return $this->auth;
    }

    public function router() {
        if (!$this->router) {
            $this->router = resolve(Router::class);
        }

        return $this->router;
    }

    public function assetManager() {
        if (!$this->assetManager) {
            $this->assetManager = resolve(Asset::class);
        }

        return $this->assetManager;
    }

    /**
     * @return Vue
     */
    public function vueManager() {
        if (!$this->vueManager) {
            $this->vueManager = resolve(Vue::class);
        }

        return $this->vueManager;
    }

}
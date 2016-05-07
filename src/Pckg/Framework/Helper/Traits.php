<?php namespace Pckg\Framework\Helper;

use Pckg\Framework\Request;
use Pckg\Framework\Request\Data\Post;
use Pckg\Framework\Request\Data\Session;
use Pckg\Framework\Response;
use Pckg\Auth\Service\Auth;

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
     * @var Session
     */
    private $session;

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
     * @return Post
     */
    public function post()
    {
        if (!$this->post) {
            $this->post = resolve(Post::class);
        }

        return $this->post;
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

    public function auth()
    {
        if (!$this->auth) {
            $this->auth = resolve(Auth::class);
        }

        return $this->auth;
    }

}
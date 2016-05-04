<?php namespace Pckg\Framework\Helper;

use Pckg\Framework\Request;
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

    public function auth()
    {
        if (!$this->auth) {
            $this->auth = resolve(Auth::class);
        }

        return $this->auth;
    }

}
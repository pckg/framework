<?php

namespace Pckg\Framework\Request;

use Pckg\Framework\Helper\Lazy;
use Pckg\Framework\Request;
use Pckg\Framework\Request\Data\Post;
use Pckg\Framework\Request\Data\Cookie;
use Pckg\Framework\Request\Data\Get;
use Pckg\Framework\Request\Data\Server;

class MockRequest extends Request
{
    public function __construct()
    {
        $this->post = new Post([]);
        $this->get = new Get([]);
        $this->server = new Server($_SERVER);
        $this->files = new Lazy([]);
        $this->cookie = new Cookie([]);
        $this->request = new Request\Data\Request([]);
        $this->headers = [];

        $this->fetchUrl();
    }
}

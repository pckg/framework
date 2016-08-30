<?php

namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Helper\Lazy;

class Server extends Lazy
{

    function __construct()
    {
        parent::__construct($_SERVER);
    }

    public function __destruct()
    {
        $_SERVER = $this->data;
    }

}

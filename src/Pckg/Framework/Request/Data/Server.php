<?php

namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Helper\Lazy;
use Pckg\Framework\Request\Message;
use Psr\Http\Message\ServerRequestInterface;

class Server extends Lazy /*extends Message implements ServerRequestInterface*/
{

    public function __construct($arr = [])
    {
        parent::__construct($_SERVER ?? $arr);
    }

    public function __destruct()
    {
    }
}

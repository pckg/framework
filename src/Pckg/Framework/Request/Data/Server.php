<?php

namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Helper\Lazy;
use pckg\Framework\Request\Message;
use Psr\Http\Message\ServerRequestInterface;

class Server extends Lazy /*extends Message implements ServerRequestInterface*/
{

    function __construct()
    {
        parent::__construct($_SERVER);
    }

    public function __destruct()
    {
    }
    
    

}

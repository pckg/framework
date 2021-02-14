<?php

namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Helper\Lazy;
use Pckg\Framework\Request\Message;
use Psr\Http\Message\ServerRequestInterface;

class Server extends Lazy /*extends Message implements ServerRequestInterface*/
{

    public function setFromGlobals()
    {
        $this->setData($_SERVER);

        return $this;
    }
}

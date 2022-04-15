<?php

namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Helper\Lazy;

class Server extends Lazy
{
    public function setFromGlobals()
    {
        $this->setData($_SERVER);

        return $this;
    }
}

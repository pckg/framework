<?php

namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Helper\Lazy;

class Request extends Lazy
{
    public function setFromGlobals()
    {
        $this->setData($_REQUEST);

        return $this;
    }
}

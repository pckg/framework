<?php

namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Helper\Lazy;

class Get extends Lazy
{

    public function setFromGlobals()
    {
        $this->setData($_GET);

        return $this;
    }
}

<?php

namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Helper\Lazy;

class Files extends Lazy
{

    public function setFromGlobals()
    {
        $this->setData($_FILES);

        return $this;
    }
}

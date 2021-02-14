<?php

namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Helper\Lazy;

class Post extends Lazy
{

    public function setFromGlobals()
    {
        $this->setData($_POST);

        return $this;
    }
}

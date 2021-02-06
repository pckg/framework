<?php

namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Helper\Lazy;

class Get extends Lazy
{

    public function __construct($arr = [])
    {
        parent::__construct($_GET ?? $arr);
    }

    public function __destruct()
    {
        $_GET = $this->data;
    }
}

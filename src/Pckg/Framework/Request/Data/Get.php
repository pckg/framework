<?php

namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Helper\Lazy;

class Get extends Lazy
{

    function __construct()
    {
        parent::__construct($_GET);
    }

    function __destruct()
    {
        $_GET = $this->data;
    }

}

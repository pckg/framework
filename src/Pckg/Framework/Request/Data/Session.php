<?php

namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Helper\Lazy;

class Session extends Lazy
{

    function __destruct()
    {
        $_SESSION = $this->data;
    }

}

?>
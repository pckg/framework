<?php

namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Helper\Lazy;

class Get extends Lazy
{

    function __construct(&$_get = [])
    {
        if (empty($_get)) {
            $_get = $_GET;
        }

        parent::__construct($_get);
    }

}

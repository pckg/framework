<?php

namespace Pckg\Request\Data;

class Get extends Lazy
{
    function __construct(&$_get = [])
    {
        if (empty($_get)) $_get = $_GET;

        parent::__construct($_get);
    }
}

?>
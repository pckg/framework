<?php

namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Helper\Lazy;

class Post extends Lazy
{
    function __construct(&$_post = [])
    {
        if (empty($_post)) $_post = $_POST;

        parent::__construct($_post);
    }
}

?>
<?php

namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Helper\Lazy;

class Post extends Lazy
{

    function __construct()
    {
        parent::__construct($_POST);
    }

    public function __destruct()
    {
        $_POST = $this->data;
    }

}

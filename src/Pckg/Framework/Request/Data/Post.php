<?php

namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Helper\Lazy;

class Post extends Lazy
{

    public function __construct($arr = [])
    {
        parent::__construct($arr ?? $_POST);
    }

    public function __destruct()
    {
        //$_POST = $this->data;
    }
}

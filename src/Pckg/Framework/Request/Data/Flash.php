<?php

namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Helper\Lazy;

class Flash extends Lazy
{

    public function __construct()
    {
        parent::__construct(isset($_SESSION) && isset($_SESSION['Flash'])
                ? $_SESSION['Flash']
                : []);
    }

    public function set($name, $val)
    {
        $this->__set($name, $val);
        $this->__destruct();
        return $this;
    }

    public function __destruct()
    {
        $_SESSION['Flash'] = $this->data;
    }

    public function get($name, $delete = true)
    {
        $value = parent::get($name);
        if ($delete) {
            $this->__unset($name);
        }

        return $value;
    }
}

<?php

namespace Pckg\Framework\View;

interface ViewInterface
{

    function __construct($file, $data = []);

    public function addData($key, $val = null);

    public function getData($key = null);

    public function setData($data = []);

    public function autoparse();

    public function __toString();

}
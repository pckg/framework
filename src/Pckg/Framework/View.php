<?php

namespace Pckg\Framework;

use Pckg\Framework\View\Twig;

class View
{

    protected static $dirs = [];
    protected $view;
    protected $data;

    function __construct($view = null, $data = [])
    {
        $this->createTwig($view, $data);
    }

    public function createTwig($view = null, $data = [])
    {
        return $this->view = new Twig($view, $data);
    }

    public function create($view = null, $data = [])
    {
        return new self($view, $data);
    }

    public function addData($arrData = [])
    {
        return $this->view->addData($arrData);
    }

    public function getData()
    {
        return $this->view->getData();
    }

    public function setData($arrData)
    {
        return $this->view->setData($arrData);
    }

    public function autoparse()
    {
        return $this->view->autoparse();
    }

    public function __toString()
    {
        return (string)$this->view->__toString();
    }
}

?>
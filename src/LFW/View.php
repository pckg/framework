<?php

namespace Pckg;

class View
{

    protected $view;
    protected $data;

    protected static $dirs = [];

    function __construct($view = NULL, $data = [])
    {
        $this->createTwig($view, $data);
    }

    public function create($view = NULL, $data = [])
    {
        return new self($view, $data);
    }

    public function createTwig($view = NULL, $data = [])
    {
        return $this->view = new \LFW\View\Twig($view, $data);
    }

    public function setData($arrData)
    {
        return $this->view->setData($arrData);
    }

    public function addData($arrData = [])
    {
        return $this->view->addData($arrData);
    }

    public function getData()
    {
        return $this->view->getData();
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
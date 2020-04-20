<?php namespace Pckg\Framework\Test;

use Pckg\Concept\Context;

trait TestHelper
{


    /**
     * @var \UnitTester
     */
    protected $tester;
    
    /**
     * @var Context
     */
    protected $context;

    protected function _before()
    {
        include "vendor/autoload.php";
        $run = $this->getPckgBootstrap();
        $this->context = $run('scintilla');
    }

    /**
     * @return mixed
     */
    protected function getPckgBootstrap()
    {
        return include "vendor/pckg/framework/src/bootstrap.php";
    }

}
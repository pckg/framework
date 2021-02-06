<?php

namespace Pckg\Framework\Test;

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

    /**
     * Migrated from _before()
     */
    protected function beforeTestHelper()
    {
        return;
        include "vendor/autoload.php";
        $run = $this->getPckgBootstrap();
        $this->context = $run($this->app);
    }

    /**
     * @return mixed
     */
    protected function getPckgBootstrap()
    {
        return include "vendor/pckg/framework/src/bootstrap.php";
    }
}

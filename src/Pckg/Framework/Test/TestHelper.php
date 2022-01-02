<?php

namespace Pckg\Framework\Test;

use Pckg\Concept\Context;

trait TestHelper
{
    use MockFramework;
    

    /**
     * Migrated from _before()
     */
    protected function beforeTestHelper()
    {
        include "vendor/autoload.php";
        $run = $this->getPckgBootstrap();
        $this->context = $run($this->app);
    }
}

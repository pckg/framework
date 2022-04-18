<?php

namespace Pckg\Framework\Test\Codeception;

use Codeception\Specify;
use Pckg\Framework\Test\MockFramework;
use UnitTester;

trait Cest
{
    //use Specify;
    use MockFramework;

    // @phpstan-ignore-next-line
    public function _before(UnitTester $I)
    {
        if (!defined('__ROOT__')) {
            // @phpstan-ignore-next-line
            define('__ROOT__', codecept_root_dir());
        }
        $this->tester = $I;

        if (!isset($this->disableFramework)) {
            $this->mockFramework();
        }
    }
}

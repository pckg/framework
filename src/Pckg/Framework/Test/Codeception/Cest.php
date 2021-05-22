<?php

namespace Pckg\Framework\Test\Codeception;

use Pckg\Framework\Test\MockFramework;
use UnitTester;

class Cest
{
    use MockFramework;

    // @phpstan-ignore-next-line
    protected UnitTester $unitTester;

    // @phpstan-ignore-next-line
    public function _before(UnitTester $I)
    {
        if (!defined('__ROOT__')) {
            // @phpstan-ignore-next-line
            define('__ROOT__', codecept_root_dir() . '/');
        }
        $this->unitTester = $I;
        $this->mockFramework();
    }
}

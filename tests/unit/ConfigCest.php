<?php

class ConfigCest
{

    use \Pckg\Framework\Test\MockFramework;

    public function _before(UnitTester $I)
    {
        if (!defined('__ROOT__')) {
            define('__ROOT__', realpath(__DIR__ . '/../..') . '/');
        }
        $this->mockFramework();
    }

    // tests
    public function defaultConfigTest(UnitTester $I)
    {
        $config = new \Pckg\Framework\Config();

        $I->assertEquals([], $config->get(), 'Default should be []');

        $I->assertEquals(true, $config->get('foo', true), 'Unexistent with default true should be true');
        $I->assertEquals(false, $config->get('foo', false), 'Unexistent with default false should be false');
        $I->assertEquals([], $config->get('foo', []), 'Unexistent with default [] should be []');

        $I->assertEquals(true, $config->get('foo.bar', true), 'Unexistent dotted with default true should be true');
        $I->assertEquals(false, $config->get('foo.bar', false), 'Unexistent dotted with default false should be false');
        $I->assertEquals([], $config->get('foo.bar', []), 'Unexistent dotted with default [] should be []');
    }
}

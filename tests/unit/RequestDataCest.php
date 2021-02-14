<?php

class RequestDataCest
{

    use \Pckg\Framework\Test\MockFramework;

    public function _before(UnitTester $I)
    {
        define('__ROOT__', realpath(__DIR__ . '/../..') . '/');
        $this->mockFramework();
    }

    // tests
    public function defaultRequestDataTest(UnitTester $I)
    {
        $request = new Pckg\Framework\Request();
        $I->assertEquals([], $request->get()->all());
        $I->assertEquals([], $request->post()->all());
        $I->assertNotEquals([], $request->server()->all());
        $I->assertEquals([], $request->cookie()->all());
        $I->assertEquals([], $request->files()->all());
        $I->assertEquals([], $request->request()->all());
    }
}

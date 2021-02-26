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

    // tests
    public function filledRequestDataTest(UnitTester $I)
    {
        $_POST = ['foo' => 'post'];
        $_GET = ['foo' => 'get'];
        $_COOKIE = ['foo' => 'cookie'];
        $_REQUEST = ['foo' => 'request'];
        $request = new Pckg\Framework\Request();

        $I->assertEquals(['foo' => 'get'], $request->get()->all());
        $I->assertEquals(['foo' => 'post'], $request->post()->all());
        $I->assertNotEquals([], $request->server()->all());
        $I->assertEquals(['foo' => 'cookie'], $request->cookie()->all());
        $I->assertEquals([], $request->files()->all());
        $I->assertEquals(['foo' => 'request'], $request->request()->all());

        $staticSource = new \Pckg\Framework\Request\Data\PostResolver\StaticSource();
        $staticSource->writeToSource(['foo' => 'static']);
        $request->post()->setSource($staticSource);
        $request->post()->setFromGlobals();
        $I->assertEquals(['foo' => 'static'], $request->post()->all());
    }
}

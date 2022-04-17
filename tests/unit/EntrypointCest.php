<?php

class EntrypointCest
{

    public function _before(UnitTester $I)
    {
        if (!defined('__ROOT__')) {
            define('__ROOT__', realpath(__DIR__ . '/../..') . '/');
        }
        //$this->mockFramework();
    }

    // tests
    public function bootstrapTest(UnitTester $I)
    {
        $included = require __ROOT__ . 'src/bootstrap.php';

        $I->assertIsCallable($included);

        $context = $included(null, null);
        $I->assertEquals(\Pckg\Framework\Helper\Context::class, get_class($context));

        $context2 = $included(null, null);
        $I->assertEquals(\Pckg\Framework\Helper\Context::class, get_class($context2));

        $I->assertNotSame($context, $context2);
    }

    public function productionTest(UnitTester $I)
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            unset($_SERVER['HTTP_HOST']);
        }
        $I->assertTrue(!isset($_SERVER['HTTP_HOST']));

        try {
            require __ROOT__ . 'src/production.php';
            throw new Exception('Exception not thrown');
        } catch (Throwable $e) {
            $I->assertEquals('Class Test not found', $e->getMessage());
        }

        try {
            $_SERVER['HTTP_HOST'] = 'test';
            require __ROOT__ . 'src/production.php';
            throw new Exception('Exception not thrown');
        } catch (Throwable $e) {
            $I->assertEquals('Class Test not found', $e->getMessage());
        }
    }

    public function developmentTest(UnitTester $I)
    {
        return;
        if (isset($_SERVER['HTTP_HOST'])) {
            unset($_SERVER['HTTP_HOST']);
        }
        $I->assertTrue(!isset($_SERVER['HTTP_HOST']));

        try {
            require __ROOT__ . 'src/development.php';
            throw new Exception('Exception not thrown');
        } catch (Throwable $e) {
            $I->assertEquals('Class Test not found', $e->getMessage());
        }

        try {
            $_SERVER['HTTP_HOST'] = 'test';
            require __ROOT__ . 'src/development.php';
            throw new Exception('Exception not thrown');
        } catch (Throwable $e) {
            $I->assertEquals('Class Test not found', $e->getMessage());
        }
    }

    public function consoleTest(UnitTester $I)
    {
        $_SERVER['argv'] = [
            'console',
            'test',
        ];
        $_SERVER['argc'] = 2;

        try {
            require __ROOT__ . 'src/console.php';
            throw new Exception('Exception not thrown');
        } catch (Throwable $e) {
            $I->assertEquals('Class Test not found', $e->getMessage());
        }
    }
}

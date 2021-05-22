<?php

use Pckg\Framework\Application\Command\InitDatabase;
use Pckg\Framework\Test\ContextDiff;
use Pckg\Framework\Test\MockFramework;

class FrameworkApplicationCommandInitDatabaseCest
{

    use MockFramework;
    use ContextDiff;

    protected UnitTester $unitTester;

    public function _before(UnitTester $I)
    {
        if (!defined('__ROOT__')) {
            define('__ROOT__', realpath(__DIR__ . '/../..') . '/');
        }
        $this->unitTester = $I;
        $this->mockFramework();
    }

    protected function _after()
    {
    }

    protected function createInitDatabaseObject(array $config = []): InitDatabase
    {
        return new InitDatabase(new \Pckg\Framework\Config($config));
    }

    public function testUnsetDatabaseList(): void
    {
        $this->checkContextDiff(function () {
            $this->createInitDatabaseObject([])->execute(fn() => null);
        }, 0);
    }

    public function testEmptyDatabaseList(): void
    {
        $this->checkContextDiff(function () {
            $this->createInitDatabaseObject([
                'database' => [],
            ])->execute(fn() => null);
        }, 0);
    }

    public function testSingleDatabaseRegistration(): void
    {
        $this->checkContextDiff(function () {
            $this->createInitDatabaseObject([
                'database' => [
                    'default' => [
                        'driver' => 'json',
                        'db' => 'foo',
                    ],
                ],
            ])->execute(fn() => null);
        }, 2);
    }

    public function testDoubleDatabaseRegistration(): void
    {
        $this->checkContextDiff(function () {
            $this->createInitDatabaseObject([
                'database' => [
                    'default' => [
                        'driver' => 'json',
                        'db' => 'foo',
                    ],
                    'alternative' => [
                        'driver' => 'json',
                        'db' => 'bar',
                    ],
                ],
            ])->execute(fn() => null);
        }, 3);
    }
}

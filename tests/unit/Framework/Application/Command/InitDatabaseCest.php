<?php

namespace Test\Framework\Application\Command;

use Pckg\Framework\Application\Command\InitDatabase;
use Pckg\Framework\Test\Codeception\Cest;
use Pckg\Framework\Test\ContextDiff;

class InitDatabaseCest extends Cest
{

    use ContextDiff;

    protected \UnitTester $unitTester;

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

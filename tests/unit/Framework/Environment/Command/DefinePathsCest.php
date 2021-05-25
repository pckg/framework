<?php

namespace Test\Framework\Config\Command;

use Pckg\Framework\Environment\Command\DefinePaths;
use Pckg\Framework\Test\Codeception\Cest;
use Pckg\Framework\Test\MockConfig;

class DefinePathsCest extends Cest
{

    public function testConfig()
    {
        $paths = [
            'ds' => '/',
            'root' => '/var/www/html/',
            'apps' => '/var/www/html/app/',
            'src' => '/var/www/html/src/',
            'storage' => '/var/www/html/storage/',
            'private' => '/var/www/html/storage/private/',
            'public' => '/var/www/html/storage/public/',
            'www' => '/var/www/html/www/',
            'cache' => '/var/www/html/storage/cache/',
            'tmp' => '/var/www/html/storage/tmp/',
            'uploads' => '/var/www/html/storage/uploads/',
            'vendor' => '/var/www/html/vendor/',
            'build' => '/var/www/html/build/',
        ];

        $preRegistered = true;
        if (!$preRegistered) {
            foreach ($paths as $key => $val) {
                try {
                    path($key);
                    throw new \Exception('Miss-exception ' . $key);
                } catch (\Throwable $e) {
                    $this->tester->assertNotEquals('Miss-exception ' . $key, $e->getMessage());
                }
            }

            (new DefinePaths())->execute(fn() => null);
        }

        foreach ($paths as $key => $val) {
            $this->tester->assertEquals($val, path($key), 'Checking ' . $key);
        }
    }
}

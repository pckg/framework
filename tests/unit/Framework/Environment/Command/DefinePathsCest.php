<?php

namespace Test\Framework\Config\Command;

use Pckg\Framework\Environment\Command\DefinePaths;
use Pckg\Framework\Test\Codeception\Cest;
use Pckg\Framework\Test\MockConfig;

class DefinePathsCest
{
    use Cest;

    public function testConfig()
    {
        $path = codecept_root_dir();
        $paths = [
            'ds' => '/',
            'root' => $path,
            'apps' => $path . 'app/',
            'src' => $path . 'src/',
            'storage' => $path . 'storage/',
            'private' => $path . 'storage/private/',
            'public' => $path . 'storage/public/',
            'www' => $path . 'www/',
            'cache' => $path . 'storage/cache/',
            'tmp' => $path . 'storage/tmp/',
            'uploads' => $path . 'storage/uploads/',
            'vendor' => $path . 'vendor/',
            'build' => $path . 'build/',
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

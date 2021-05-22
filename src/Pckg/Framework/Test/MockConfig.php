<?php

namespace Pckg\Framework\Test;

use Pckg\Framework\Config;

trait MockConfig
{

    protected function mockConfig(array $default = [], &$reset = null)
    {
        $original = $this->context->get(Config::class);
        $reset = function () use ($original) {
            $this->context->bind(Config::class, $original);
        };
        $config = new Config($default);

        return $config;
    }
}

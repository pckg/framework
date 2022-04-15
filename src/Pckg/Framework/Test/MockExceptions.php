<?php

namespace Pckg\Framework\Test;

trait MockExceptions
{
    protected function throwException($message = 'Miss-match exception')
    {
        throw new \Exception($message);
    }

    protected function matchException($message, $e)
    {
        $this->tester->assertSame($message, $e->getMessage());
    }
}

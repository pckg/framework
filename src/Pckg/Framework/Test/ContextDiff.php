<?php

namespace Pckg\Framework\Test;

/**
 * Trait ContextDiff
 * @package Pckg\Framework\Test
 */
trait ContextDiff
{

    protected function checkContextDiff(callable $task, int $diff)
    {
        $starting = $this->context->getData();
        $response = $task();
        $ending = $this->context->getData();
        $this->unitTester->assertEquals($diff, count($ending) - count($starting));

        return $response;
    }
}

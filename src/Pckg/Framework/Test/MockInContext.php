<?php

namespace Pckg\Framework\Test;

trait MockInContext
{

    public function mockInContext($object, string $name = null)
    {
        $this->context->bind($name ?? get_class($object), $object);

        return $object;
    }
}

<?php

namespace Pckg\Framework\Request\Data\PostResolver;

class StaticSource implements PostSource
{

    protected $data = [];

    public function readFromSource(): array
    {
        return $this->data;
    }

    public function writeToSource(array $data): bool
    {
        $this->data = $data;

        return true;
    }
}

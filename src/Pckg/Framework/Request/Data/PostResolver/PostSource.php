<?php

namespace Pckg\Framework\Request\Data\PostResolver;

interface PostSource
{
    public function readFromSource(): array;

    public function writeToSource(array $data): bool;
}

<?php namespace Pckg\Framework;

class Stack
{

    protected $stacks = [];

    public function push(callable $callable)
    {
        $this->stacks[] = $callable;

        return $this;
    }

    public function execute()
    {
        foreach ($this->stacks as $callable) {
            $callable();
        }
        $this->stacks = [];

        return $this;
    }

}
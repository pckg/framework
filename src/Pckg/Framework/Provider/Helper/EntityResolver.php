<?php namespace Pckg\Framework\Provider\Helper;

trait EntityResolver
{

    public function by($field)
    {
        $this->by = $field;

        return $this;
    }

    public function parametrize($record)
    {
        return $record->{$this->by};
    }

    public function resolve($value)
    {
        $entity = $this->entity;

        return (new $entity)->where($this->by, $value)->oneOrFail();
    }

}
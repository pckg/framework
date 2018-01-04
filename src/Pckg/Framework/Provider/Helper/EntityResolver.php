<?php namespace Pckg\Framework\Provider\Helper;

use Pckg\Database\Entity;

trait EntityResolver
{

    /**
     * @var Entity
     */
    protected $e;

    public function by($field)
    {
        $this->by = $field;

        return $this;
    }

    public function parametrize($record)
    {
        return $record->{$this->by ?? 'id'};
    }

    public function resolve($value)
    {
        $entity = $this->entity;

        $this->e = (new $entity)->where($this->by ?? 'id', $value);

        if (method_exists($this, 'also')) {
            $this->also();
        }

        return $this->e->oneOrFail();
    }

}
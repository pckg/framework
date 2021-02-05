<?php

namespace Pckg\Framework\Provider\Helper;

use Pckg\Database\Entity;

trait EntityResolver
{

    /**
     * @var Entity
     */
    protected $e;

    protected $postField = null;

    protected $filter = null;

    public function by($field)
    {
        $this->by = $field;

        return $this;
    }

    public function getBy()
    {
        return $this->by ?? 'id';
    }

    public function parametrize($record)
    {
        return $record->{$this->by ?? 'id'};
    }

    public function prepareEntity()
    {
        $e = $this->entity;

        return $this->e = new $e();
    }

    public function filter(callable $filter = null)
    {
        $this->filter = $filter;

        return $this;
    }

    public function resolve($value)
    {
        if ($this->postField && !post($this->postField, null)) {
            return null;
        }

        $this->prepareEntity($value);

        $this->e->where($this->getBy(), ($this->postField ? post($this->postField, null) : $value));

        if (method_exists($this, 'also')) {
            $this->also($value);
        }

        if (isset($this->compareTo)) {
            $compared = router()->resolved($this->compareTo);
            $this->e->where('id', $compared->id);
        }

        if ($filter = $this->filter) {
            $filter($this->e, $value);
        }

        return $this->e->oneOrFail();
    }

    public function compareTo($compareTo)
    {
        $this->compareTo = $compareTo;

        return $this;
    }

    public function fromPost($postField)
    {
        $this->postField = $postField;

        return $this;
    }
}

<?php

namespace Pckg\Framework\Helper;

class TryCatch
{

    /**
     * @var mixed
     */
    protected $object;

    /**
     * @var
     */
    protected $caught;

    /**
     * @var
     */
    protected $result;

    /**
     * TryCatch constructor.
     * @param $object
     */
    public function __construct($object)
    {
        $this->object = $object;
    }

    /**
     * @param $method
     * @param $args
     * @return TryCatch
     */
    public function __call($method, $args)
    {
        try {
            $this->result = $this->object->{$method}(...$args);
        } catch (\Throwable $e) {
            $this->caught = $e;
        }

        return $this;
    }

    /**
     * @return mixed|null
     */
    public function catch()
    {
        if ($this->caught) {
            return null;
        }

        return $this->resolve();
    }

    /**
     * @return mixed
     */
    public function resolve()
    {
        return $this->result;
    }
}

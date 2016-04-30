<?php

namespace Pckg\Framework\Helper;

use ArrayAccess;

class Lazy implements ArrayAccess
{

    protected $data = [];

    protected $original = [];

    protected $parent = [];

    public function __construct($arr = [])
    {
        $this->data = $arr instanceof \stdClass
            ? (array)$arr
            : $arr;
    }

    public function __destruct()
    {
        $this->original = $this->data;

        if ($this->parent) {
            $this->parent[0]->__set($this->parent[1], $this->toArray());
        }
    }

    public function toArray()
    {
        return $this->__toArray();
    }

    public function __toArray()
    {
        if (!is_array($this->data)) {
            return $this->data;
        }

        $arr = [];
        foreach ($this->data AS $key => $val) {
            $arr[$key] = is_object($val) ? $val->__toArray() : $val;
        }

        return $arr;
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __get($name)
    {
        if (!$this->__isset($name)) {
            return null;

        } else if (!is_array($this->data[$name])) {
            return $this->data[$name];

        }

        $lazy = new Lazy($this->data[$name]);
        $lazy->setParent($this, $name);

        return $lazy;
    }

    public function __set($name, $val)
    {
        if (!$name) {
            $this->data = $name;
        } else {
            $this->data[$name] = $val;
        }

        return $this;
    }

    public function setParent($parent, $key)
    {
        $this->parent = [$parent, $key];

        return $this;
    }

    public function has($keys)
    {
        if (!is_array($keys)) {
            $keys = [$keys];
        }

        foreach ($keys as $key) {
            if (!array_key_exists($key, $this->data)) {
                return false;
            }
        }

        return true;
    }

    public function get($name)
    {
        if (!$this->__isset($name)) {
            return null;
        }

        return $this->__get($name);
    }

    public function __toInt()
    {
        return count($this->data);
    }

    public function __toString()
    {
        return (string)$this->data;
    }

    public function __toBool()
    {
        return !empty($this->data);
    }

    public function offsetSet($offset, $value)
    {
        return $this->__set($offset, $value);
    }

    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }

    public function offsetUnset($offset)
    {
        return $this->__unset($offset);
    }

    public function __unset($name)
    {
        unset($this->data[$name]);

        return $this;
    }

    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }
}

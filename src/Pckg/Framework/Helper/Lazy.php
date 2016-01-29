<?php

namespace Pckg\Framework\Helper;

class Lazy implements \ArrayAccess
{

    protected $data = [];

    protected $original = [];

    protected $parent = [];

    public function __construct(&$arr = [])
    {
        if (is_array($arr)) {
            $this->data = $arr instanceof \stdClass
                ? (array)$arr
                : $arr;
        }
    }

    public function __destruct()
    {
        $this->original = $this->data;

        if ($this->parent) {
            $this->parent[0]->__set($this->parent[1], $this->toArray());
        }
    }

    public function setParent($parent, $key)
    {
        $this->parent = [$parent, $key];

        return $this;
    }

    public function __call($name, $args)
    {
        if (isset($this->data[$name]) && is_callable($this->data[$name])) {
            return call_user_method_array($this->data[$name], $this, $args);

        } else if (substr($name, 0, 3) == "get" && $this->__isset(lcfirst(substr($name, 3)))) {
            return $this->__get(substr($name, 3));

        } else if (substr($name, 0, 3) == "set" && count($args) == 1) {
            return $this->__set(substr($name, 3), $args);

        }
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

    public function __get($name)
    {
        if (!$this->__isset($name)) {
            return null;
        }

        if (!is_array($this->data[$name]) && !is_object($this->data[$name])) {
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

    public function __unset($name)
    {
        unset($this->data[$name]);

        return $this;
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __toInt()
    {
        return 0;
    }

    public function __toString()
    {
        return (string)$this->data;
    }

    public function toArray()
    {
        return $this->__toArray();
    }

    public function __toArray()
    {
        $arr = [];
        foreach ($this->data AS $key => $val) {
            $arr[$key] = is_object($val) ? $val->__toArray() : $val;
        }

        return $arr;
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

    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }
}

?>
<?php

namespace Pckg\Framework\Helper;

use ArrayAccess;
use Pckg\Collection;

class Lazy implements ArrayAccess
{
    protected $data = [];

    protected $original = [];

    protected $parent = [];

    public function __construct($arr = [])
    {
        $this->data = $arr instanceof \stdClass
            ? (array)$arr
            : ($arr ? $arr : []);
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function setPointerData(&$data)
    {
        $this->data = $data;

        return $this;
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
        foreach ($this->data as $key => $val) {
            $arr[$key] = is_object($val) ? $val->__toArray() : $val;
        }

        return $arr;
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

    public function get($name, $default = null)
    {
        if (!$this->__isset($name)) {
            return $default;
        }

        return $this->__get($name);
    }

    public function touch($key, $default = [])
    {
        if (!$this->__isset($key)) {
            return $this->__set($key, $default);
        }

        return $this->__get($key);
    }

    public function __isset($name)
    {
        if (isset($this->data[$name])) {
            return true;
        }

        if (strpos($name, '.')) {
            return hasDotted($this->data, explode('.', $name));
        }

        return false;
    }

    public function __get($name)
    {
        if ($this->__isset($name) && array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        if (strpos($name, '.')) {
            return getDotted($this->data, explode('.', $name));
        }

        return null;
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

    public function set($name, $val)
    {
        return $this->__set($name, $val);
    }

    public function all()
    {
        return $this->data;
    }

    /**
     * @return Collection
     * @deprecated
     */
    public function collection()
    {
        return $this->collect();
    }

    /**
     * @return Collection
     */
    public function collect()
    {
        return collect($this->data);
    }

    public function __toInt()
    {
        return count($this->data);
    }

    public function __toString()
    {
        return json_encode($this->data);
    }

    public function __toBool()
    {
        return !empty($this->data);
    }

    public function offsetSet($offset, $value): void
    {
        $this->__set($offset, $value);
    }

    public function offsetExists($offset): bool
    {
        return $this->__isset($offset);
    }

    public function offsetUnset($offset): void
    {
        $this->__unset($offset);
    }

    public function __unset($name)
    {
        unset($this->data[$name]);

        return $this;
    }

    public function offsetGet($offset): mixed
    {
        return $this->__get($offset);
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}

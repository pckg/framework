<?php

namespace Pckg\Framework\View;

abstract class AbstractView implements ViewInterface
{

    protected $file = null;

    protected $data = [];

    protected static $dirs = [];

    const PRIORITY_APP = 100;

    const PRIORITY_VENDOR = 200;

    const PRIORITY_MODULE = 300;

    const PRIORITY_LAST = 500;

    public function __construct($file, $data = [])
    {
        $file = str_replace(':', '\View\\', $file);

        $this->file = $file;
        $this->data = $data;
    }

    public function getDirs()
    {
        $d = [];
        ksort(static::$dirs);
        foreach (static::$dirs as $priority => $dirs) {
            foreach ($dirs as $dir) {
                $d[] = $dir;
            }
        }

        return $d;
    }

    public function addData($key, $val = null)
    {
        if (!is_object($key) && is_array($key)) {
            foreach ($key AS $k => $v) {
                $this->addData($k, $v);
            }

            return;
        }

        if (!isset($this->data[$key])) {
            $this->data[$key] = $val;
        } else {
            $this->data[$key] .= $val;
        }
    }

    public function getData($key = null)
    {
        if (!$key) {
            return $this->data;

        } else if (array_key_exists($key, $this->data)) {
            return $this->data['key'];

        }

        return null;
    }

    public function setData($data = [])
    {
        $this->data = $data;
    }

    public function __toString()
    {
        try {
            return $this->autoparse();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public static function addDir($path, $priority = 0)
    {
        if (is_array($path)) {
            foreach ($path as $dir) {
                static::addDir($dir, $priority);
            }

            return;
        }

        $path = str_replace("\\", path('ds'), $path);

        if (!isset(static::$dirs[$priority])) {
            static::$dirs[$priority] = [];
        }

        if (!in_array($path, static::$dirs)) {
            array_push(static::$dirs[$priority], $path);
        }
    }

}
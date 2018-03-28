<?php

namespace Pckg\Framework\View;

use Throwable;

abstract class AbstractView implements ViewInterface
{

    const PRIORITY_APP = 100;

    const PRIORITY_VENDOR = 200;

    const PRIORITY_MODULE = 300;

    const PRIORITY_LAST = 500;

    protected static $staticData = [];

    protected static $dirs = [];

    protected $file = null;

    protected $data = [];

    public function hasData($key)
    {
        return array_key_exists($key, $this->data);
    }

    public function __construct($file = null, $data = [])
    {
        $file = str_replace(':', '/View/', $file);

        $this->file = $file;
        $this->data = $data;
    }

    public static function addStaticData($key, $val)
    {
        if (!is_object($key) && is_array($key)) {
            foreach ($key AS $k => $v) {
                static::addData($k, $v);
            }

            return;
        }

        if (!isset(static::$staticData[$key])) {
            static::$staticData[$key] = $val;
        } else {
            static::$staticData[$key] .= $val;
        }
    }

    public function addData($key, $val = null)
    {
        if (!is_object($key) && is_array($key)) {
            foreach ($key AS $k => $v) {
                $this->addData($k, $v);
            }

            return $this;
        }

        $this->data[$key] = $val;

        return $this;
    }

    public static function setStaticData($key, $val)
    {
        static::$staticData[$key] = $val;
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

        if (!in_array($path, static::$dirs[$priority] ?? [])) {
            array_push(static::$dirs[$priority], $path);
        }
    }

    public function getDirs()
    {
        $d = [];
        ksort(static::$dirs);
        foreach (static::$dirs as $priority => $dirs) {
            foreach ($dirs as $dir) {
                if (is_dir($dir)) {
                    $d[] = $dir;
                }
            }
        }

        return $d;
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

        return $this;
    }

    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    public function __toString()
    {
        try {
            $html = $this->autoparse();
        } catch (Throwable $e) {
            return $e->getMessage();
        }

        return $html;
    }

}
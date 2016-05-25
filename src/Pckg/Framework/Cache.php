<?php

namespace Pckg\Framework;

class Cache
{

    protected $cache = [];

    protected $cachePath;

    protected $built = false;

    public function __construct($cachePath = null)
    {
        if ($cachePath) {
            $this->cachePath = path('cache') . 'framework/' . str_replace(['\\', '/'], '_', $cachePath) . '.cache';
        }

        $this->readFromCache();

        if (!$this->built) {
            $this->buildCache();
            $this->writeToCache();
        }
    }

    protected function readFromCache()
    {
        $file = $this->getCachePath();

        if (file_exists($file)) {
            $this->cache = json_decode(file_get_contents($file), true);
            $this->built = true;
        }
    }

    protected function getCachePath()
    {
        return $this->cachePath;
    }

    protected function buildCache()
    {
    }

    public function writeToCache($cache = null)
    {
        if ($cache) {
            $this->cache = $cache;
        }

        $cachePath = $this->getCachePath();
        if (is_file($cachePath)) {
            unlink($cachePath);
        }

        file_put_contents($cachePath, json_encode($this->cache));
    }

    public function isBuilt()
    {
        return $this->built;
    }

    public function get()
    {
        return $this->cache;
    }

}
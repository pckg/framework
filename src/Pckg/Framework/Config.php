<?php

namespace Pckg\Framework;

class Config
{

    protected $data = [];

    public function initSettings()
    {
        $appConfig = $this->get();

        $this->set('domain', $appConfig['defaults']['domain']);
        $this->set('title', $appConfig['defaults']['title']);
        $this->set('protocol', $appConfig['defaults']['protocol']);
        $this->set('url', $appConfig['defaults']['protocol'] . "://" . $appConfig['defaults']['domain']);
        $this->set('hash', $appConfig['defaults']['security']['hash']);
    }

    public function get($key = null)
    {
        if (!$key) {
            return $this->data;
        }

        $value = isset($this->data[$key])
            ? $this->data[$key]
            : null;

        if (!$value && strpos($key, '.')) {
            $keys = explode('.', $key);

            return $this->recursive($keys, 0, $this->data);
        }

        return $value;
    }

    protected function recursive($keys, $i, $data)
    {
        if (!isset($keys[$i])) {
            return $data;
        } else if (isset($data[$keys[$i]])) {
            return $this->recursive($keys, $i + 1, $data[$keys[$i]]);
        }

        return null;
    }

    public function set($key, $val)
    {
        if ($key) {
            $this->data[$key] = $val;
        } else {
            $this->data = $val;
        }
    }

    public function parseDir($dir)
    {
        $configPath = $dir == path('root')
            ? 'config'
            : 'Config';

        $files = [
            "defaults" => $dir . $configPath . path('ds') . "defaults.php",
            "database" => $dir . $configPath . path('ds') . "database.php",
            "router"   => $dir . $configPath . path('ds') . "router.php",
        ];

        $settings = [];
        foreach ($files AS $key => $file) {
            $cache = new Cache($file);
            startMeasure($file);
            if ($cache->isBuilt()) {
                $settings[$key] = $cache->get();
            } else {
                $settings[$key] = is_file($file)
                    ? require $file
                    : [];
                $cache->writeToCache($settings[$key]);
            }
            stopMeasure($file);
        }

        foreach ($settings AS $key => $parsed) {
            foreach ($parsed AS $key2 => $configs) {
                $this->data[$key][$key2] = $configs;
            }
        }
    }

    public function __toArray()
    {
        return $this->data;
    }

    public function __get($key)
    {
        return isset($this->data[$key])
            ? $this->data[$key]
            : null;
    }

    protected function findKey($key, $arr)
    {

    }
}

?>
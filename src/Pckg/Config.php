<?php

namespace Pckg;

use Exception;
use Symfony\Component\Yaml\Yaml;

class Config
{

    protected $data = [];

    public function set($key, $val)
    {
        if ($key) {
            $this->data[$key] = $val;
        } else {
            $this->data = $val;
        }
    }

    public function get($key = null)
    {
        if (!$key) {
            return $this->data;
        }

        $value = isset($this->data[$key])
            ? $this->data[$key]
            : NULL;

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

    protected function findKey($key, $arr)
    {

    }

    public function initSettings()
    {
        $appConfig = $this->get();

        $this->set('domain', $appConfig['defaults']['domain']);
        $this->set('title', $appConfig['defaults']['title']);
        $this->set('protocol', $appConfig['defaults']['protocol']);
        $this->set('url', $appConfig['defaults']['protocol'] . "://" . $appConfig['defaults']['domain']);
        $this->set('hash', $appConfig['defaults']['security']['hash']);
    }

    public function parseDir($dir, $cache = true)
    {
        $yaml = new Yaml();

        $configPath = $dir == path('root')
            ? 'config'
            : 'Config';

        $files = [
            "defaults" => $dir . $configPath . path('ds') . "defaults.yml",
            "database" => $dir . $configPath . path('ds') . "database.yml",
            "router" => $dir . $configPath . path('ds') . "router.yml",
        ];

        $settings = [];
        foreach ($files AS $key => $file) {
            $settings[$key] = is_file($file)
                ? $yaml->parse(file_get_contents($file))
                : [];
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
}

?>
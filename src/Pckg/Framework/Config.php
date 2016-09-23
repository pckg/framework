<?php

namespace Pckg\Framework;

class Config
{

    protected $data = [];

    public function initSettings()
    {
        $appConfig = $this->get();

        $this->set('domain', $appConfig['domain']);
        $this->set('title', $appConfig['title']);
        $this->set('protocol', $appConfig['protocol']);
        $this->set('url', $appConfig['protocol'] . "://" . $appConfig['domain']);
        $this->set('hash', $appConfig['security']['hash']);
    }

    public function get($key = null)
    {
        if (!$key) {
            return $this->data;
        }

        $value = array_key_exists($key, $this->data)
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
        $files = [
            "defaults" => $dir . 'config' . path('ds') . "defaults.php",
            "database" => $dir . 'config' . path('ds') . "database.php",
            "router"   => $dir . 'config' . path('ds') . "router.php",
            "env"      => $dir . 'config' . path('ds') . "env.php",
        ];

        $settings = [];
        foreach ($files AS $key => $file) {
            $cache = new Cache($file);
            if (false && $cache->isBuilt()) {
                startMeasure('Reading from cache: ' . $file);
                if ($key == 'defaults') {
                    // defaults
                    $settings = array_merge_recursive($settings, $cache->get());
                } else {
                    $settings[$key] = $cache->get();
                }
                stopMeasure('Reading from cache: ' . $file);
            } else {
                startMeasure('Building cache: ' . $file);
                $content = is_file($file)
                    ? require $file
                    : [];
                if ($key == 'defaults') {
                    // defaults
                    $settings = array_merge_recursive($settings, $content);
                } else {
                    $settings[$key] = $content;
                }
                //$cache->writeToCache($settings[$key]);
                stopMeasure('Building cache: ' . $file);
            }
        }

        foreach ($settings AS $key => $parsed) {
            if (is_array($parsed)) {
                foreach ($parsed AS $key2 => $configs) {
                    $this->data[$key][$key2] = $configs;
                }
            } else {
                $this->data[$key] = $parsed;
            }
        }

        if (isset($this->data['env'])) {
            foreach ($this->data['env'] as $key => $val) {
                if (is_array($val)) {
                    foreach ($val as $key2 => $val2) {
                        if (is_array($val2)) {
                            $this->data[$key][$key2] = array_merge($this->data[$key][$key2] ?? [], $val2);

                        } else {
                            $this->data[$key][$key2] = $val2;

                        }
                    }
                } else {
                    $this->data[$key] = $val;
                }
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
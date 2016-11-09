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

        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        if (strpos($key, '.')) {
            $keys = explode('.', $key);

            return $this->recursive($keys, 0, $this->data);
        }

        return null;
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
        if (!$dir) {
            return;
        }

        $cache = new Cache($dir);
        $settings = [];
        if (false && $cache->isBuilt()) {
            $settings = $cache->get();

        } else {
            /**
             * @T00D00
             * We need to parse config directory recursively.
             * defaults.php and env.php needs to be taken differently (as root namespace).
             */
            $files = [
                "defaults" => $dir . 'config' . path('ds') . "defaults.php",
                "database" => $dir . 'config' . path('ds') . "database.php",
                "router"   => $dir . 'config' . path('ds') . "router.php",
                "env"      => $dir . 'config' . path('ds') . "env.php",
            ];

            foreach ($files AS $key => $file) {
                $content = is_file($file)
                    ? require $file
                    : [];
                if (in_array($key, ['defaults', 'env'])) {
                    $settings = $this->merge($settings, $content);
                } else {
                    $settings[$key] = $content;
                }
            }
        }

        $this->data = $this->merge($this->data, $settings);
    }

    private function merge($to, $merge)
    {
        foreach ($merge as $key => $val) {
            /**
             * Value is set first time.
             */
            if (!array_key_exists($key, $to)) {
                $to[$key] = $val;
                continue;
            }

            /**
             * Value is final.
             */
            if (!is_array($val)) {
                $to[$key] = $val;
                continue;
            }

            /**
             * Value is list of items.
             */
            if (array_key_exists(0, $val)) {
                $to[$key] = $val;
                continue;
            }

            /**
             * Value is keyed array.
             */
            $to[$key] = $this->merge($to[$key], $val);
        }

        return $to;
    }

    public function __toArray()
    {
        return $this->data;
    }

    public function __get($key)
    {
        return array_key_exists($key, $this->data)
            ? $this->data[$key]
            : null;
    }

}

?>
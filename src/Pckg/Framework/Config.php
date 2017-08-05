<?php

namespace Pckg\Framework;

class Config
{

    protected $data = [];

    public function get($key = null, $default = null)
    {
        if (!$key) {
            return $this->data;
        }

        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        if (strpos($key, '.')) {
            $keys = explode('.', $key);

            return getDotted($this->data, $keys);
        }

        return $default;
    }

    public function set($key, $val)
    {
        /**
         * Get all keys, separated by dot.
         */
        if (strpos($key, '.')) {
            $keys = explode('.', $key);
        } else {
            $keys = [$key];
        }

        $this->data = $this->setRecursive($keys, $val, $this->data, 0);
    }

    private function setRecursive($keys, $val, $data, $i)
    {
        if ($i >= count($keys)) {
            $data = $val;

            return $data;
        }

        $key = $keys[$i];

        if (!is_array($data)) {
            $data = $val;

            return $data;
        }

        if (!array_key_exists($key, $data)) {
            $data[$key] = [];
        }

        $data[$key] = $this->setRecursive($keys, $val, $data[$key], $i + 1);

        return $data;
    }

    public function parseDir($dir)
    {
        if (!$dir) {
            return;
        }

        /*$cache = new Cache($dir);*/

        $settings = /*false && $cache->isBuilt()
            ? $cache->get()
            : */
            $this->parseFiles($this->getDirFiles($dir . 'config/'));

        $this->data = merge_arrays($this->data, $settings);

        $this->set('url', config('protocol') . "://" . config('domain'));
    }

    protected function parseFiles($files)
    {
        $settings = [];

        foreach ([
                     function($file) { return strpos($file, '/defaults.php'); },
                     function($file) { return !strpos($file, '/defaults.php') && !strpos($file, '/env.php'); },
                     function($file) { return strpos($file, '/env.php'); },
                 ] as $callback) {
            foreach ($files AS $key => $file) {
                if (!$callback($file)) {
                    continue;
                }

                $content = require $file;

                if (in_array($key, ['defaults', 'env'])) {
                    /**
                     * @T00D00
                     * We need to parse config directory recursively.
                     * defaults.php and env.php needs to be taken differently (as root namespace).
                     */
                    $settings = merge_arrays($settings, $content);
                } else {
                    if (strpos($key, '.')) {
                        $keys = explode('.', $key);
                        $content = $this->setRecursive($keys, $content, [], 0);
                        if (isset($settings[$keys[0]])) {
                            $settings[$keys[0]] = merge_arrays($settings[$keys[0]] ?? [], $content[$keys[0]]);
                        } else {
                            $settings[$keys[0]] = $content[$keys[0]];
                        }
                    } else {
                        $settings[$key] = $content;
                    }
                }
            }
        }

        return $settings;
    }

    protected function getDirFiles($dir, $prefix = '')
    {
        if (!is_dir($dir)) {
            return [];
        }

        $files = [];
        $scanned = scandir($dir);

        if ($prefix) {
            $prefix .= '.';
        }
        foreach ($scanned as $item) {
            if (in_array($item, ['.', '..'])) {
                continue;
            } else if (strpos($item, '.sample')) {
                continue;
            }

            if (is_dir($dir . $item)) {
                foreach ($this->getDirFiles($dir . $item . '/', $prefix . $item) as $subkey => $subfile) {
                    $files[$subkey] = $subfile;
                }
            } else if (is_file($dir . $item) && strrpos($item, '.php') == strlen($item) - strlen('.php')) {
                $subkey = substr($item, 0, -strlen('.php'));
                $files[$prefix . $subkey] = $dir . $item;
            }
        }

        return $files;
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
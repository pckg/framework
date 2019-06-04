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

    public function set($key, $val, $merge = true)
    {
        /**
         * Get all keys, separated by dot.
         */
        $keys = explode('.', $key);

        $this->data = $this->setRecursive($keys, $val, $this->data, 0, $merge);
    }

    private function setRecursive($keys, $val, $data, $i, $merge = true)
    {
        if (!is_array($data)) {
            /**
             * Data is string, integer, float, callable, null.
             * We can fully overwrite it.
             */
            return $val;
        }

        if ($i >= count($keys)) {
            /**
             * We want to merge existing values.
             */
            if ($merge && is_array($val) && $data) {
                return array_merge($data, $val);
            }

            /**
             * Key doesn't exist in config yet.
             * We can fully overwrite it.
             */
            return $val;
        }

        $key = $keys[$i];

        if (!array_key_exists($key, $data)) {
            /**
             * Key doesn't exist in config yet.
             * We can fully overwrite it.
             */
            $data[$key] = [];
        }

        $data[$key] = $this->setRecursive($keys, $val, $data[$key], $i + 1, $merge);

        return $data;
    }

    public function parseDir($dir)
    {
        if (!$dir) {
            return;
        }

        /*$cache = new Cache($dir);*/

        /**
         * @T00D00 - we can cache settings by hash of all dir files.
         */
        $settings = /*false && $cache->isBuilt()
            ? $cache->get()
            : */
            $this->parseFiles($this->getDirFiles($dir . 'config/'));

        $this->data = merge_arrays($this->data, $settings);

        $this->set('url', "https://" . config('domain'));
    }

    protected function parseFiles($files)
    {
        $settings = [];

        foreach ([
                     function($file) { return strpos($file, '/defaults.php'); },
                     function($file) {
                         return !strpos($file, '/defaults.php') && !strpos($file, '/env.php') &&
                                !strpos($file, '/migrations.php');
                     },
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
                    $keys = explode('.', $key);
                    /**
                     * Key is for example pckg.generic.templates.
                     * [pckg, generic, templates]
                     * We will create $content = pckg => [generic => [templates => $content]]
                     */
                    $content = $this->setRecursive($keys, $content, [], 0);
                    /**
                     * Then we merge it with existent settings.
                     */
                    $settings[$keys[0]] = merge_arrays($settings[$keys[0]] ?? [], $content[$keys[0]], $keys[0]);
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

    public function hasKey($key)
    {
        return hasDotted($this->data, explode('.', $key));
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

    public function getPublicConfig()
    {
        $public = [];
        foreach (config('pckg.config.public', []) as $key => $callback) {
            $public[$key] = $callback($this);
        }

        return base64_encode(json_encode($public));
    }

}

?>
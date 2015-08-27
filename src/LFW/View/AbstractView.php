<?php

namespace Pckg\View;

abstract class AbstractView implements ViewInterface
{

    protected $file = null;

    protected $data = [];

    protected static $dirs = [];

    public function __construct($file, $data = [])
    {
        $this->file = $file;
        $this->data = $data;
    }

    public function getDirs() {
        $d = [];
        asort(static::$dirs);
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
        return $this->autoparse();
    }

    public static function addDir($path, $priority = 0)
    {
        if (is_array($path)) {
            foreach($path as $dir) {
                static::addDir($dir);
            }
            return;
        }
        $path = str_replace("\\", path('ds'), $path);

        if (!in_array($path, static::$dirs)) {
            static::$dirs[$priority][] = $path;
        }
    }

    function addDirForFile($dbFile)
    {
        if (strpos($dbFile, path('vendor')) === 0) {
            $relative = substr($dbFile, strlen(path('vendor')));
            $vendor = implode('/', array_slice(explode('/', $relative), 0, 2));

            $absolute = path('vendor') . $vendor . path('ds');
            $this->addDir($absolute);

            if (is_dir($absolute . 'src' . path('ds'))) {
                $this->addDir($absolute . 'src' . path('ds'));
            }
        } else {
            foreach ([ // get relative file names
                         str_replace(path('app_src'), "", $dbFile),
                         str_replace(path('src'), "", $dbFile),
                         str_replace(path('root'), "", $dbFile), // is this needed?
                     ] AS $file) {
                foreach ([ // replace
                             path('app_src') . substr($file, 0, strpos($file, "/Controller/")) . "/View/",
                             path('src') . substr($file, 0, strpos($file, "/Controller/")) . "/View/",
                             path('root') . substr($file, 0, strpos($file, "/Controller/")) . "/View/",
                         ] AS $dir) {
                    if (is_dir($dir)) {
                        $this->addDir($dir);
                    }
                }
            }
        }
    }

    protected function setLoaders($file)
    {
        $prevDirs = static::$dirs;

        static::$dirs = [];

        // app's folder
        $this->addDir(path('app_src'));

        // shared src folder
        $this->addDir(path('src'));

        // injected apps

        foreach (router()->getResources() AS $resource) {
            $src = isset($resource['src']['src']) ? $resource['src']['src'] : $resource['src'];

            $this->addDir(path('root') . $src . path('ds'));

            if (substr($src, 0, 7) == 'vendor' . path('ds')) {
                $this->addDir(path('root') . implode(path('ds'), array_slice(explode(path('ds'), $src), 0, 3)) . path('ds'));
            }
        }

        if (substr($file, 0, 1) != "/") { // add namespace
            $db = debug_backtrace();

            // 0 and 1 are twig and context
            foreach (range(2, 7) AS $i) { // @T00D00 - make this prettier
                if (!isset($db[$i]["file"])) {
                    // we have nothing to do here
                    continue;
                } else if (!strpos($db[$i]["file"], '/Controller/')) {
                    // we look for controllers only
                    continue;
                }

                $this->addDirForFile($db[$i]["file"]);
            }
        }

        foreach ($prevDirs AS $dir) {
            $this->addDir($dir);
        }
    }

}
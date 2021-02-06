<?php

namespace Pckg\Framework\Router\Route;

class Route
{
    use Merger;

    protected $url;

    protected $controller;

    protected $view;

    protected $name;

    protected $resolvers = [];

    protected $afterwares = [];

    protected $middlewares = [];

    protected $data = [];

    protected $methods;

    public function __construct($url = null, $view = null, $controller = null)
    {
        $this->url($url);
        $this->view($view);
        $this->controller($controller);
    }

    public function url($url)
    {
        $this->url = $url;

        return $this;
    }

    public function name($name)
    {
        $this->name = $name;

        return $this;
    }

    public function controller($controller)
    {
        $this->controller = $controller;

        return $this;
    }

    public function view($view)
    {
        $this->view = $view;
    }

    public function register($parentData)
    {
        $mains = ['url', 'controller', 'view', 'name', 'resolvers', 'afterwares', 'middlewares'];
        $data = $this->data;
        /**
         * First get defaults.
         */
        foreach ($mains as $main) {
            if (!isset($this->{$main})) {
                continue;
            }

            if ($value = $this->{$main}) {
                $data[$main] = $value;
            }
        }

        if ($this->methods) {
            $data['method'] = $this->methods;
        }

        /**
         * Then merge them with parent data.
         */
        $mergedData = $this->mergeData($parentData, $data);

        /**
         * And apply missing defaults.
         */
        foreach ($mains as $main) {
            if (!isset($this->{$main})) {
                continue;
            }

            if (!isset($mergedData[$main])) {
                $data[$main] = $this->{$main};
            }
        }

        $url = $mergedData['url'] ?? '@';
        $name = $mergedData['name'] ?? '@';
        router()->add($url, $mergedData, $name);

        return [$url, $mergedData, $name];
    }

    public function resolvers($resolvers = [])
    {
        $this->resolvers = $resolvers;

        return $this;
    }

    public function afterwares($afterwares = [])
    {
        $this->afterwares = $afterwares;

        return $this;
    }

    public function middlewares($middlewares = [])
    {
        $this->middlewares = $middlewares;

        return $this;
    }

    public function data($data = [])
    {
        $this->data = $data;

        return $this;
    }

    public function mergeToData($data = [])
    {
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if (is_int($k2)) {
                        $this->data[$k][] = $v2;
                        continue;
                    }
                    $this->data[$k][$k2] = $v2;
                }
                continue;
            }

            $this->data[$k] = $v;
        }

        return $this;
    }

    public function methods($methods)
    {
        $this->methods = $methods;

        return $this;
    }
}

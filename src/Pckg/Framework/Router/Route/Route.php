<?php namespace Pckg\Framework\Router\Route;

class Route
{

    use Merger;

    protected $url;

    protected $controller;

    protected $view;

    protected $name;

    protected $resolvers = [];

    protected $afterwares = [];

    protected $data = [];

    protected $methods = [];

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
        $mains = ['url', 'controller', 'view', 'name', 'resolvers', 'afterwares'];
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

        router()->add($mergedData['url'] ?? '@', $mergedData, $mergedData['name'] ?? '@');
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

    public function data($data = [])
    {
        $this->data = $data;

        return $this;
    }

    public function methods($methods)
    {
        $this->methods = $methods;

        return $this;
    }

}
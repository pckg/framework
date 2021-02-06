<?php

namespace Pckg\Framework\Router\Route;

class Group
{

    protected $data = [];

    protected $routes = [];

    protected $groups = [];

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    public function routes($routes = [])
    {
        $this->routes = $routes;

        return $this;
    }

    public function groups($groups = [])
    {
        $this->groups = $groups;

        return $this;
    }

    public function register($parentData = [])
    {
        /**
         * Merge parent data with current group data.
         */
        $mergedData = array_merge($parentData, $this->data);

        /**
         * Register groups.
         */
        foreach ($this->groups as $group) {
            $group->register($mergedData);
        }

        /**
         * Register routes.
         */
        foreach ($this->routes as $name => $route) {
            $route->name($name);
            $route->register($mergedData);
        }

        return $this;
    }

    /**
     * @param array $resolvers
     */
    public function resolvers(array $resolvers = [])
    {
        foreach ($this->routes as $route) {
            $route->resolvers($resolvers);
        }

        return $this;
    }
}

<?php namespace Pckg\Framework\Router\Route;

class VueRoute extends Route
{

    protected $children = [];

    public function children(array $children = [])
    {
        $this->children = $children;

        return $this;
    }

    public function register($parentData)
    {
        $vueChildRoutes = [];
        if ($this->children) {
            $vueChildRoutes = collect(array_keys($this->children))->map(function ($name) {
                return $this->name . '.' . $name;
            })->values();
            $this->data['tags']['vue:route:children'] = $vueChildRoutes;
        }

        [$url, $mergedData, $name] = parent::register($parentData);

        unset($parentData['tags']['vue:route:children']);

        foreach ($this->children as $key => $childRoute) {
            /**
             * @var $childRoute VueRoute
             */
            $childRoute->data['tags'][] = 'vue:route:child';
            $parentData['urlPrefix'] = $url;
            $parentData['namePrefix'] = $name . '.' . $key;
            $childRoute->register($parentData);
        }

        return [$url, $mergedData, $name];
    }

}
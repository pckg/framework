<?php

namespace Pckg\Framework\Router\Route;

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
            $vueChildRoutes = collect($this->children)
                ->realReduce(function (VueRoute $vueRoute, $name, $reduce) {
                    $reduce[] = $this->name . '.' . trim($name, '.');
                    $reduce = collect($vueRoute->children)->realReduce(function (VueRoute $vueRoute, $subname, $reduce) use ($name) {
                        $reduce[] = $this->name . '.' . trim($name, '.') . '.' . trim($subname, '.');
                        return $reduce;
                    }, $reduce);
                    return $reduce;
                }, []);
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
            $childRoute->inheritResolvers($this);
            $childRoute->register($parentData);
        }

        return [$url, $mergedData, $name];
    }

    public function seo(array $array = [])
    {
        foreach ($array as $key => $val) {
            $this->data['tags']['seo:' . $key] = $val;
        }

        return $this;
    }
}

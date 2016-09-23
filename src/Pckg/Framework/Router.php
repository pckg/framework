<?php

namespace Pckg\Framework;

use Pckg\Concept\Reflect;
use Pckg\Framework\View\Twig;

class Router
{

    protected $cache;

    protected $resolved = [];

    private $routes = [];

    private $cachedInit = [];

    private $resources = [];

    private $prefix;

    private $data = [];

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function init()
    {
        $cache = $this->getCache();

        autoloader()->add('', path('app') . 'src');
        Twig::addDir(path('app') . 'src' . path('ds'));

        if (!dev() && $cache->isBuilt()) {
            $this->initDev();

        } else {
            $this->initProd();

        }

        return $this;
    }

    public function getCache()
    {
        if (!$this->cache) {
            $this->cache = new Cache(
                'framework/router_' . str_replace(
                    [
                        '\\',
                        '/',
                    ],
                    '_',
                    (get_class(app()) . '_' . get_class(env()))
                ) . '.cache'
            );
        }

        return $this->cache;
    }

    protected function initDev()
    {
        $cache = $this->getCache();
        $data = $cache->get();
        $this->routes = $data['routes'];
        $this->cachedInit = $data['cachedInit'];

        if (isset($this->cachedInit['autoloader'])) {
            foreach ($this->cachedInit['autoloader'] as $dir) {
                autoloader()->add('', $dir);
            }
        }

        if (isset($this->cachedInit['view'])) {
            foreach ($this->cachedInit['view'] as $dir) {
                Twig::addDir($dir);
            }
        }
    }

    protected function initProd()
    {
        $router = $this->config->get('router');

        if (isset($router['providers'])) {
            foreach ($router['providers'] AS $providerType => $arrProviders) {
                foreach ($arrProviders AS $provider => $providerConfig) {
                    $routeProvider = Reflect::create(
                        'Pckg\\Framework\\Router\\Provider\\' . ucfirst($providerType),
                        [
                            $providerType => $provider,
                            'config'      => $providerConfig,
                            'name'        => $provider,
                        ]
                    );
                    $routeProvider->init();
                }
            }
        }

        $this->writeCache();
    }

    public function writeCache()
    {
        $this->getCache()->writeToCache(
            [
                'routes'     => $this->routes,
                'cachedInit' => $this->cachedInit,
            ]
        );
    }

    public function addCachedInit($cachedInit = [])
    {
        $this->cachedInit = array_merge($this->cachedInit, $cachedInit);

        return $this;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function setPrefix($prefix = null)
    {
        $this->prefix = $prefix;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function add($route, $conf = [], $name = null)
    {
        $conf["name"] = $name;
        $conf["url"] = $route;

        if (!isset($this->routes[$conf["url"]])) {
            $this->routes[$conf["url"]] = [];
        }

        array_unshift($this->routes[$conf["url"]], $conf);
    }

    public function getResources()
    {
        return $this->resources;
    }

    public function make($routeName = null, $arguments = [], $absolute = false)
    {
        if (!$routeName) {
            $routeName = $this->data["name"];
        }

        foreach ($this->routes AS $routeArr) {
            foreach ($routeArr AS $route) {
                if ($route["name"] == $routeName) {
                    $args = [];
                    foreach ($arguments AS $key => $val) {
                        $args["[" . $key . "]"] = $val;
                    }

                    foreach ($route['resolvers'] ?? [] as $key => $resolver) {
                        /**
                         * If index is not set, argument should be resolved by post/get data or similar.
                         * T00D00 - this needs to be resolved without proper index (find by class)
                         */
                        if (isset($args['[' . $key . ']'])) {
                            $args['[' . $key . ']'] = resolve($resolver)->parametrize($args['[' . $key . ']']);
                        }
                    }

                    if ($args && isset($route['resolvers'])) {
                        /**
                         * Replace parameters in url.
                         */
                        $route['url'] = str_replace(array_keys($args), $args, $route['url']);
                    }

                    return (
                           $absolute
                               ? $this->config->get("protocol") . '://' .
                                 ($this->config->get("domain") ?? $_SERVER['HTTP_HOST'])
                               : "") .
                           (dev()
                               ? "/dev.php"
                               : ""
                           ) . $route["url"];
                }
            }
        }
    }

    public function get($param = null, $default = [])
    {
        return $param ?
            (isset($this->data[$param])
                ? $this->data[$param]
                : $default)
            : $this->data;
    }

    public function getURL($relative = true)
    {
        return $this->getUri($relative);
    }

    public function getUri($relative = true)
    {
        return ($relative ? '' : $this->config->get("url")) . $_SERVER['REQUEST_URI'];
    }

    public function getName()
    {
        return $this->data['name'] ?: null;
    }

    public function mergeData($data)
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    public function resolve($key, $val)
    {
        $this->resolved[$key] = $val;

        return $this;
    }

    public function resolved($key = null)
    {
        return $key
            ? $this->resolved[$key]
            : $this->resolved;
    }

    public function hasResolved($key)
    {
        return array_key_exists($key, $this->resolved);
    }

}

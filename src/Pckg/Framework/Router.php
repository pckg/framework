<?php

namespace Pckg\Framework;

use Pckg\Cache\Cache;
use Pckg\Collection;
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

    public function getRoute($url)
    {
        return $this->routes[$url][0] ?? null;
    }

    public function init()
    {
        $cache = $this->getCache();

        if (false && !dev() && $cache->isBuilt()) {
            $this->initDev();
        } else {
            $this->initProd();
        }

        return $this;
    }

    public function getCache()
    {
        if (!$this->cache) {
            $this->cache = new Cache('framework/router_' . str_replace([
                                                                           '\\',
                                                                           '/',
                                                                       ], '_',
                                         (get_class(app()) . '_' . get_class(env()))) . '.cache');
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
                    $routeProvider = Reflect::create('Pckg\\Framework\\Router\\Provider\\' . ucfirst($providerType), [
                                                                                                                       $providerType => $provider,
                                                                                                                       'config'      => $providerConfig,
                                                                                                                       'name'        => $provider,
                                                                                                                   ]);
                    $routeProvider->init();
                }
            }
        }

        $this->writeCache();
    }

    public function writeCache()
    {
        $this->getCache()->writeToCache([
                                            'routes'     => $this->routes,
                                            'cachedInit' => $this->cachedInit,
                                        ]);
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

    public function add($route, $conf = [], $name = null, $domain = null)
    {
        $conf = array_merge($conf, [
                                     'name'   => $name,
                                     'url'    => $route === '/' ? '/' : rtrim($route, '/'),
                                     'domain' => $domain,
                                 ]);

        if (!isset($this->routes[$conf["url"]])) {
            $this->routes[$conf["url"]] = [];
        }

        array_push($this->routes[$conf["url"]], $conf);
    }

    public function replace($route, $conf = [])
    {
        $this->routes[$route][0] = merge_arrays($this->routes[$route][0], $conf);

        return $this;
    }

    public function getResources()
    {
        return $this->resources;
    }

    public function getRouteByName($name)
    {
        foreach ($this->routes AS $routeArr) {
            foreach ($routeArr AS $route) {
                if ($route["name"] == $name) {
                    return $route;
                }
            }
        }

        foreach ($this->routes AS $routeArr) {
            foreach ($routeArr AS $route) {
                if (strpos($route["name"], $name . ':') === 0) {
                    return $route;
                }
            }
        }

        return null;
    }

    public function getRoutesByName($name)
    {
        $routes = [];
        foreach ($this->routes AS $routeArr) {
            foreach ($routeArr AS $route) {
                if ($route["name"] == $name || strpos($route['name'], $name . ':') === 0) {
                    $routes[] = $route;
                    break;
                }
            }
        }

        return $routes;
    }

    public function removeRouteByName($routeName)
    {

        foreach ($this->routes AS $i => $routeArr) {
            foreach ($routeArr AS $j => $route) {
                /**
                 * Remove non-translated route.
                 */
                if ($route["name"] == $routeName) {
                    unset($this->routes[$i][$j]);
                }
                /**
                 * Remove translated route.
                 */
                if (strpos($route['name'], $routeName . ':') === 0) {
                    unset($this->routes[$i][$j]);
                }
            }
            /**
             * Remove empty routes.
             */
            if (!$this->routes[$i]) {
                unset($this->routes[$i]);
            }
        }

        return $this;
    }

    public function getRoutePrefix($absolute = false, $domain = null, $envPrefix = true)
    {
        $host = $absolute || isConsole()
            ? 'https://' . first($domain, server('HTTP_HOST'), config('domain'))
            : '';

        $env = $envPrefix && dev() && !isConsole() ? '/dev.php' : '';

        return $host . $env;
    }

    public function make($routeName = null, $arguments = [], $absolute = false, $envPrefix = true)
    {
        if (!$routeName) {
            $routeName = $this->data["name"];
        }

        foreach ($this->routes AS $routeArr) {
            foreach ($routeArr AS $route) {
                if ($route['name'] != $routeName &&
                    $route['name'] != $routeName . ':' . config('pckg.locale.language')) {
                    continue;
                }

                $args = [];
                /**
                 * $arguments = ['packet' => $packet];
                 * $args = ['[packet]' => $packet];
                 */
                foreach ($arguments AS $key => $val) {
                    $args["[" . $key . "]"] = $val;
                }

                foreach ($route['resolvers'] ?? [] as $key => $resolver) {
                    /**
                     * If index is not set, argument should be resolved by post/get data or similar.
                     * T00D00 - this needs to be resolved without proper index (find by class)
                     */
                    if (isset($args['[' . $key . ']']) && is_object($args['[' . $key . ']'])) {
                        $realResolver = is_only_callable($resolver) ? $resolver() : (is_object($resolver)
                            ? $resolver
                            : resolve($resolver));
                        $recordObject = $args['[' . $key . ']'];
                        $args['[' . $key . ']'] = $realResolver->parametrize($recordObject);
                        /**
                         * Add auto resolved slugged value.
                         */
                        if (strpos($route['url'], '[' . $key . 'Url]') !== false) {
                            $originalTitle = trim($recordObject->title);
                            if (!$originalTitle) {
                                $originalTitle = $recordObject->id;
                            }
                            $args['[' . $key . 'Url]'] = $originalTitle;
                        }
                    }
                }

                if ($args) {
                    /**
                     * Replace parameters in url.
                     */
                    foreach ($args as $key => &$arg) {
                        if (is_string($arg)) {
                            if (strpos(strtolower($key), 'url') !== false) {
                                $arg = trim(substr(sluggify($arg), 0, 42), '-');
                            } else {
                                $arg = urlencode($arg);
                            }
                        }
                    }
                    $filteredArgs = (new Collection($args))->reduce(function($item) {
                        return !is_object($item);
                    }, true)->all();
                    $route['url'] = str_replace(array_keys($filteredArgs), $filteredArgs, $route['url']);
                }

                return $this->getRoutePrefix($absolute, $route['domain'] ?? null, $envPrefix) . $route["url"];
            }
        }

        return $this->getRoutePrefix($absolute, null, $envPrefix);
    }

    public function get($param = null, $default = [])
    {
        return $param ? (isset($this->data[$param]) ? $this->data[$param] : $default) : $this->data;
    }

    public function getURL($relative = true)
    {
        return $this->getUri($relative);
    }

    public function getUri($relative = true)
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';

        return ($relative ? '' : $this->config->get("url")) .
            ((strpos($requestUri, '?') === false ? $requestUri : substr($requestUri, 0, strpos($requestUri, '?'))) ??
                '/');
    }

    public function getCleanUri($relative = true)
    {
        return str_replace(['/dev.php/', '/index.php/'], '/', $this->getUri($relative));
    }

    public function getName()
    {
        return $this->data['name'] ?? null;
    }

    public function getCssName()
    {
        return str_replace(['.'], '-', strtolower($this->getName()));
    }

    public function setData($data = [])
    {
        $this->data = $data;

        return $this;
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
        return $key ? (array_key_exists($key, $this->resolved) ? $this->resolved[$key] : null) : $this->resolved;
    }

    public function getResolves()
    {
        return $this->resolved;
    }

    public function hasResolved($key)
    {
        return array_key_exists($key, $this->resolved);
    }

    public function hasUrl($name)
    {
        return $this->getRouteByName($name) ? true : false;
    }

    public function getPublicRoutes()
    {
        $publicRoutes = config('pckg.router.publicRoutes', []);
        $allRoutes = router()->getRoutes();
        $routes = [];
        foreach ($publicRoutes as $route) {
            if (strpos($route, '(.*)') === false) {
                $routes[$route] = url($route);
                continue;
            }
            foreach ($allRoutes as $key => $routeArr) {
                $firstRoute = $routeArr[0];
                if (!preg_match('~^' . $route . '$~', $firstRoute['name'])) {
                    continue;
                }
                $routes[$firstRoute['name']] = url($firstRoute['name']);
            }
        }

        return $routes;
    }

    protected function transformVueRoute($route, $prefix = '')
    {
        $tags = $route['tags'] ?? [];
        $url = $prefix . $route['url'];
        $url = str_replace(['[', ']'], [':', ''], $url);
        $vueRoute = [
            'path' => $url,
            'name' => $route['name'],
        ];
        if (array_key_exists('vue:route:redirect', $tags)) {
            $vueRoute['redirect'] = $prefix . $tags['vue:route:redirect'];
        }

        /**
         * When there's a layout, the layout should render the component.
         */
        $component = null;
        foreach ($tags as $k => $v) {
            if (strpos($v, 'layout:') === 0) {
                if ($v === 'layout:frontend') {
                    $component = '<pb-route-layout></pb-route-layout>';
                } else if ($v === 'layout:backend') {
                } else {
                    $component = substr($v, strlen('layout:'));
                    $component = '<' . $component . '></' . $component . '>';
                }
                break;
            }
        }

        /**
         * VueJS.
         */
        if ($component) {
            $vueRoute['component'] = ['name' => sluggify($component), 'template' => $component];
        } else if (array_key_exists('vue:route:template', $tags)) {
            $vueRoute['component'] = ['name' => sluggify($tags['vue:route:template']), 'template' => $tags['vue:route:template']];
        } else {
            //$vueRoute['component'] = 'pb-router-layout';
        }

        if (array_key_exists('vue:route:children', $tags)) {
            $routes = $this->getRoutes();
            $vueRoute['children'] = [];
            foreach ($tags['vue:route:children'] as $key) {
                $foundRoute = null;
                foreach ($routes as $url => $routesArr) {
                    if ($routesArr[0]['name'] == $key) {
                        $foundRoute = $routesArr[0];
                    }
                }
                if (!$foundRoute) {
                    continue;
                }
                $childRoute = $this->transformVueRoute($foundRoute, $prefix);
                $vueRoute['children'][] = $childRoute;
            }
        }

        /**
         * Frontend tags are objectized. ['vue:route', 't' => 'x'] becomes {'vue:route':true,'t':'x'}
         */
        $finalTags = new \stdClass();
        foreach ($tags as $k => $v) {
            if (is_numeric($k)) {
                $finalTags->{$v} = true;
            } else {
                $finalTags->{$k} = $v;
            }
        }
        $vueRoute['meta']['tags'] = $finalTags;

        /**
         * This needs to be available on the frontend so we can resolve the params on navigation.
         */
        $vueRoute['meta']['resolves'] = array_keys($route['resolvers'] ?? []);

        /**
         * This should be put to current route.
         */
        $vueRoute['meta']['resolved'] = $route['url'] === request()->getMatch('url') ? router()->getResolves() : [];

        return $vueRoute;
    }

    public function getVueRoutes()
    {
        $allRoutes = $this->getRoutes();
        $vueRoutes = [];
        $isLoggedIn = auth()->isLoggedIn();
        $isAdmin = auth()->isAdmin();
        foreach ($allRoutes as $url => $routeArr) {
            $firstRoute = $routeArr[0];
            $tags = $firstRoute['tags'] ?? [];
            /**
             * Skip non-vue routes, and non-vue child routes
             */
            if (!in_array('vue:route', $tags)) {
                continue;
            }
            /**
             * Why skip child routes?
             */
            if (false && in_array('vue:route:child', $tags)) {
                continue;
            }
            /**
             * Skip auth routes for non-auth users.
             */
            if (!$isLoggedIn && in_array('auth:in', $tags)) {
                continue;
            }
            /**
             * Skip admin routes for non-admin users.
             */
            if (!$isAdmin && in_array('group:admin', $tags)) {
                continue;
            }
            /**
             * Build vue route.
             */
            $vueRoutes[] = $this->transformVueRoute($firstRoute);
            if (dev()) {
                $vueRoutes[] = $this->transformVueRoute($firstRoute, '/dev.php');
            }
        }
        return $vueRoutes;
    }
    
    public function mock(callable $task)
    {
        return context()->mock(new Router(config()), $task);
    }

}

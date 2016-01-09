<?php

namespace Pckg\Framework;

use Pckg\Framework\Router\Provider\App;
use Pckg\Framework\Router\RouteProviderInterface;
use Pckg\Framework\Helper\Reflect;
use Pckg\Framework\View\Twig;

class Router
{
    private $routes = [];
    private $cachedInit = [];
    private $resources = [];
    private $prefix;

    private $controller;
    private $view;
    private $data;

    protected $cache;

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

        } else {
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

        return $this;
    }

    public function addCachedInit($cachedInit = [])
    {
        $this->cachedInit = array_merge($this->cachedInit, $cachedInit);

        return $this;
    }

    public function getCache()
    {
        if (!$this->cache) {
            $this->cache = new Cache('framework/router_' . str_replace([
                    '\\',
                    '/',
                ], '_', (get_class(app()) . '_' . get_class(env()))) . '.cache');
        }

        return $this->cache;
    }

    public function writeCache()
    {
        $this->getCache()->writeToCache([
            'routes' => $this->routes,
            'cachedInit' => $this->cachedInit,
        ]);
    }

    public function setPrefix($prefix = null)
    {
        $this->prefix = $prefix;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function firendly($string, $maxLength = 80, $separator = '-')
    {
        $url = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
        $url = preg_replace('/[^a-zA-Z0-9 -]/', '', $url);
        $url = trim(substr(strtolower($url), 0, $maxLength));
        $url = preg_replace('/[s' . $separator . ']+/', $separator, $url);

        return $url;
    }

    public function add($route, $conf = [], $name = null)
    {
        $conf["name"] = $name;
        $conf["url"] = $route;

        if (!isset($this->routes[$conf["url"]])) $this->routes[$conf["url"]] = [];

        array_unshift($this->routes[$conf["url"]], $conf);
    }

    public function getResources()
    {
        return $this->resources;
    }

    public function make($routeName = null, $arguments = [], $absolute = false)
    { // @ToDo
        if (!$routeName) {
            $routeName = $this->data["name"];
        }

        foreach ($this->routes AS $routeArr)
            foreach ($routeArr AS $route)
                if ($route["name"] == $routeName) {
                    $args = [];
                    foreach ($arguments AS $key => $val) {
                        $args["[" . $key . "]"] = $val;
                    }
                    if ($args)
                        $route['url'] = str_replace(array_keys($args), $args, $route['url']);

                    return ($absolute ? $this->config->get("defaults.protocol") . '://' . $this->config->get("defaults.domain") : "") . ($this->config->get("dev") ? "/dev.php" : "") . $route["url"];
                }
    }

    public function get($param = null)
    {
        return $param ?
            (isset($this->data[$param])
                ? $this->data[$param]
                : null)
            : $this->data;
    }

    public function getUri($relative = true)
    {
        return ($relative ? '' : $this->config->get("url")) . $_SERVER['REQUEST_URI'];
    }

    public function getURL($relative = true)
    {
        return $this->getUri($relative);
    }

    public function getName()
    {
        return $this->data['name'] ?: null;
    }

    public function findMatch($url = null)
    {
        // exact match
        $found = false;
        $match = false;

        //file_put_contents(path('cache') . 'framework/router.printr', print_r($this->routes, true));
        //var_dump($this->routes);

        foreach ($this->routes AS $routeArr) {
            foreach ($routeArr AS $route) {
                if (($route["url"] == $url || $route["url"] == $url . '/') && !(strpos($url, "[") || strpos($url, "]"))) {
                    // validate method
                    if (isset($route['method']) && !empty($route['method']) && !in_array(strtolower($_SERVER['REQUEST_METHOD']), explode("|", $route['method']))) {
                        break;
                    }

                    // validate secure
                    if (isset($route['secure']) && is_callable($route['secure']) && !$route['secure']()) {
                        break;
                    }

                    $found = true;
                    $match = $route;
                    break;
                }
            }
        }

        if (!$found) {
            $arrUrl = explode("/", substr($url, 1));
            foreach ($this->routes AS $routeArr) {
                foreach ($routeArr AS $conf) {
                    $arrRoutes = explode("/", substr($conf["url"], 1));

                    // check only urls longer than routes
                    if (count($arrRoutes) > count($arrUrl)) {
                        continue;
                    }

                    // validate method
                    if (isset($conf['method']) && !empty($conf['method']) && !in_array(strtolower($_SERVER['REQUEST_METHOD']), explode("|", $conf['method']))) {
                        continue;
                    }

                    // validate secure
                    if (isset($conf['secure']) && is_callable($conf['secure']) && !$conf['secure']()) {
                        continue;
                    }

                    $error = false;
                    $regexData = [];
                    for ($i = 0; $i < count($arrUrl); $i++) {
                        if (!isset($arrRoutes[$i])) {
                            if ($arrRoutes[$i - 1] == "*") {
                                // ok
                                break;
                            } else {
                                $error = true;
                                break;
                            }
                        } else if ($arrRoutes[$i] == $arrUrl[$i]) {
                            // ok
                            continue;
                        } else if (substr($arrRoutes[$i], 0, 1) == "[" && substr($arrRoutes[$i], -1) == "]") {
                            $var = substr($arrRoutes[$i], 1, -1);

                            // validate url parts
                            if (isset($conf["validate"][$var])) {
                                if (is_callable($conf["validate"][$var])) {
                                    if ($conf["validate"][$var]($arrUrl[$i]) == true) {
                                        $regexData[$var] = $arrUrl[$i];
                                        // ok
                                    } else {
                                        $error = true;
                                        break;
                                    }
                                } else if ($conf["validate"][$var] == "int") {
                                    if (is_int($arrUrl[$i])) {
                                        $regexData[$var] = $arrUrl[$i];
                                        // ok
                                    } else {
                                        $error = true;
                                        break;
                                    }
                                } else if ($conf["validate"][$var] == "string") {
                                    if (is_string($arrUrl[$i])) {
                                        $regexData[$var] = $arrUrl[$i];
                                        // ok
                                    } else {
                                        $error = true;
                                        break;
                                    }
                                } else if ($var == "id") {
                                    if (is_int($arrUrl[$i]) && $arrUrl[$i] > 0) {
                                        $regexData[$var] = $arrUrl[$i];
                                        // ok
                                    } else {
                                        $error = true;
                                        break;
                                    }
                                } else if (is_array($conf["validate"][$var]) && in_array($arrUrl[$i], $conf["validate"][$var])) {
                                    $regexData[$var] = $arrUrl[$i];
                                    // ok
                                } else if (is_string($conf["validate"][$var]) && preg_match($conf["validate"][$var], $arrUrl[$i])) {
                                    $regexData[$var] = $arrUrl[$i];
                                    // ok
                                } else {
                                    $error = true;
                                    break;
                                }
                            } else {
                                $regexData[$var] = $arrUrl[$i];
                                // ok
                            }
                        } else if ($arrRoutes[$i] == "*") {
                            // ok
                        } else {
                            $error = true;
                            break;
                        }

                        if ($error == true) {
                            break;
                        }
                    }

                    if ($error == false) {
                        $match = $conf;
                        $match["data"] = $regexData;
                        $found = true;
                        break;
                    }
                }

                if ($found) {
                    break;
                }
            }

            if (!$found) {
                return false;
            }
        }

        // fill & transform required values
        $this->sanitizeMatch($match);

        if (!$match["controller"])
            throw new \Exception("Controller not set." . print_r($match, true));

        if (!$match["view"])
            throw new \Exception("View not set.");

        $this->controller = $match['controller'];
        $this->view = $match['view'];
        $this->data = isset($match['data']) ? $match['data'] : [];

        $this->data["controller"] = $this->controller;
        $this->data["view"] = $this->view;
        $this->data["name"] = $match['name'];
        $this->data["url"] = $match['url'];

        return $match;
    }

    protected function sanitizeMatch(&$match)
    {
        $configDefaults = $this->config->get();

        $arrTypes = ["controller", "view"];
        foreach ($arrTypes AS $type) {
            if (isset($match[$type])) {
                continue;
            }

            $match[$type] = isset($match["data"][$type])
                ? $match["data"][$type]
                : null;
        }
    }
}

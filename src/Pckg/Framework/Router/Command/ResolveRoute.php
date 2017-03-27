<?php namespace Pckg\Framework\Router\Command;

use Exception;
use Pckg\Framework\Router;

class ResolveRoute
{

    protected $router;

    protected $url;

    public function __construct(Router $router, $url)
    {
        $this->router = $router;
        $this->url = $url;
    }

    public function execute()
    {
        $url = $this->url;
        $routes = $this->router->getRoutes();

        // exact match
        $found = false;
        $match = false;

        foreach ($routes AS $routeArr) {
            foreach ($routeArr AS $route) {
                if (($route["url"] == $url || $route["url"] == $url . '/') && !(strpos($url, "[") || strpos(
                            $url,
                            "]"
                        ))
                ) {
                    // validate method
                    if (isset($route['method']) && !empty($route['method']) && !in_array(
                            strtolower($_SERVER['REQUEST_METHOD']),
                            explode("|", $route['method'])
                        )
                    ) {
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
            foreach ($routes AS $routeArr) {
                foreach ($routeArr AS $conf) {
                    $arrRoutes = explode("/", substr($conf["url"], 1));

                    // check only urls longer than routes
                    if (count($arrRoutes) > count($arrUrl)) {
                        continue;
                    }

                    // validate method
                    if (isset($conf['method']) && !empty($conf['method']) && !in_array(
                            strtolower($_SERVER['REQUEST_METHOD']),
                            explode("|", $conf['method'])
                        )
                    ) {
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
                                } else if (is_array($conf["validate"][$var]) && in_array(
                                        $arrUrl[$i],
                                        $conf["validate"][$var]
                                    )
                                ) {
                                    $regexData[$var] = $arrUrl[$i];
                                    // ok
                                } else if (is_string($conf["validate"][$var]) && preg_match(
                                        $conf["validate"][$var],
                                        $arrUrl[$i]
                                    )
                                ) {
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

        if (!$match['method']) {
            $match['method'] = 'GET|POST';
        }

        if (!$match["controller"]) {
            throw new Exception("Controller not set." . print_r($match, true));
        }

        if (!$match["view"]) {
            throw new Exception("View not set.");
        }

        $this->router->mergeData(array_merge(isset($match['data']) ? $match['data'] : [], $match));

        return $match;
    }

}
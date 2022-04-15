<?php

namespace Pckg\Framework\Router\Command;

use Exception;
use Pckg\Framework\Router;

class ResolveRoute
{
    protected $router;
    protected $url;
    protected $domain;
    public function __construct(Router $router, $url, $domain = null)
    {
        $this->router = $router;
        $this->url = $url;
        $this->domain = $domain;
    }

    public function execute()
    {
        $url = $this->url;
        $routes = $this->router->getRoutes();
// exact match
        $found = false;
        $match = false;

        foreach ($routes as $routeArr) {
            foreach ($routeArr as $route) {
                if (
                    ($route["url"] == $url || $route["url"] == $url . '/') && !(strpos($url, "[")
                        || strpos($url, "]"))
                ) {
        // validate method
                    if (
                        isset($route['method']) && !empty($route['method']) && !in_array(strtoupper($_SERVER['REQUEST_METHOD']), explode("|", strtoupper($route['method'])))
                    ) {
                /**
                                         * Check next resolved route.
                                         */
                            continue;
                    }

                    /**
                     * @deprecated
                     */
                    if (isset($route['secure']) && is_only_callable($route['secure']) && !$route['secure']()) {
                        break;
                    }

                    if ($route['language'] ?? false) {
                        if ($this->domain) {
                            if ($route['domain'] && $route['domain'] != $this->domain) {
                                break;
                            }
                        } else if ($route['language'] != localeManager()->getDefaultFrontendLanguage()->slug) {
                            break;
                        }
                    }

                    $found = true;
                    $match = $route;
                    break;
                }
            }
        }

        if (!$found) {
            $arrUrl = explode("/", substr($url, 1));
            foreach ($routes as $routeArr) {
                foreach ($routeArr as $conf) {
                    // validate language
                    if ($conf['language'] ?? false) {
                        if ($this->domain) {
                            if ($conf['domain'] && ($conf['domain'] !== $this->domain)) {
                                continue;
                            }
                        } else if (localeManager()->getDefaultFrontendLanguage()->slug !== $conf['language']) {
                            continue;
                        }
                    }

                    // validate method
                    $methods = $conf['method'] ?? null;
                    if ($methods && !is_array($methods)) {
                        $methods = explode('|', $methods);
                    }
                    if ($methods) {
                        $methods = collect($methods)->mapFn('strtolower')->all();
                        if (!in_array(strtolower(server()->get('REQUEST_METHOD', '')), $methods)) {
                            continue;
                        }
                    }

                    $arrRoutes = explode("/", substr($conf["url"], 1));

                    // skip urls shorter than routes
                    if (count($arrRoutes) > count($arrUrl)) {
                        continue;
                    }

                    // extract parameters
                    $error = false;
                    $regexData = [];
                    for ($i = 0; $i < count($arrUrl); $i++) {
                        // break when URL is longer
                        // error when not wildcard
                        if (!isset($arrRoutes[$i])) {
                            $error = $arrRoutes[$i - 1] !== '*';
                            break;
                        }

                        $routePart = $arrRoutes[$i] ?? null;
                        $urlPart = $arrUrl[$i] ?? null;

                        // full match
                        if ($routePart === $urlPart) {
                            continue; // ok
                        }

                        // anything
                        if ($routePart === '*') {
                            continue; // ok // break?
                        }

                        // not dynamic
                        if (strpos($routePart, '[') === false) {
                            $error = true;
                            break;
                        }

                        // match parameters
                        $matches = null;
                        $matched = preg_match_all("/\[[^\]]*\]/", $routePart, $matches);
                        if (!$matched) {
                            $error = true;
                            break;
                        }

                        // replace matched parts with regex
                        $tempRoutePart = $routePart;
                        foreach ($matches[0] as $match) {
                            $tempRoutePart = str_replace($match, '(?<' . substr($match, 1, -1) . '>\S+)', $tempRoutePart);
                        }

                        // perform regex matching
                        $matches = null;
                        if (!preg_match_all('/^' . $tempRoutePart . '$/', $urlPart, $matches, PREG_SET_ORDER)) {
                            $error = true;
                            break;
                        }

                        // get parameters
                        foreach ($matches[0] as $key => $val) {
                            if (is_string($key)) {
                                $regexData[$key] = $val;
                            }
                        }
                    }

                    if (!$error) {
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
        }

        if (!$found) {
            return null;
        }

        $match['method'] = $match['method'] ?? 'GET|POST';
        if (!isset($match["view"])) {
            throw new Exception("View not set.");
        }

        return $match;
    }
}

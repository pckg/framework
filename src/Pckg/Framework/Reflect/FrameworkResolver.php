<?php

namespace Pckg\Framework\Reflect;

use Pckg\Auth\Entity\Adapter\Auth;
use Pckg\Concept\Context;
use Pckg\Concept\Reflect;
use Pckg\Concept\Reflect\Resolver;
use Pckg\Framework\Config;
use Pckg\Framework\Provider;
use Pckg\Framework\Request;
use Pckg\Framework\Request\Data\Flash;
use Pckg\Framework\Response;
use Pckg\Framework\Router;
use Pckg\Locale\Lang;
use Pckg\Manager\Asset as AssetManager;
use Pckg\Manager\Locale;
use Pckg\Manager\Meta as MetaManager;
use Pckg\Manager\Seo as SeoManager;

class FrameworkResolver implements Resolver
{

    protected static $singletones = [
        Router::class,
        Context::class,
        Config::class,
        AssetManager::class,
        MetaManager::class,
        SeoManager::class,
        Flash::class,
        Response::class,
        Request::class,
        Lang::class,
        Auth::class,
        Locale::class,
    ];

    protected static $parents = [
        Provider::class,
    ];

    public function canResolve($class)
    {
        if (!class_exists($class) && !interface_exists($class)) {
            return false;
        }

        if (in_array($class, static::$singletones)) {
            return true;
        }

        foreach (static::$singletones as $singleton) {
            if (object_implements($singleton, $class)) {
                return true;
            }
        }

        return false;
    }

    public function resolve($class, $data = [])
    {
        if (!class_exists($class) && !interface_exists($class)) {
            return false;
        }

        if (in_array($class, static::$singletones)) {
            if (context()->exists($class)) {
                return context()->get($class);
            }
        } else {
            foreach (static::$parents as $parent) {
                if (in_array($parent, class_parents($class))) {
                    if (context()->exists($class)) {
                        return context()->get($class);
                    }
                }
            }
            foreach (static::$singletones as $singleton) {
                if (object_implements($singleton, $class)) {
                    if (context()->exists($class)) {
                        return context()->get($class);
                    }
                }
            }
        }

        if (class_exists($class)) {
            if (in_array($class, static::$singletones)) {
                $mockedClass = config('pckg.reflect.singletones.' . $class, $class);
                $newInstance = Reflect::create($mockedClass, $data);

                context()->bind($class, $newInstance);

                return $newInstance;
            }

            foreach (static::$parents as $parent) {
                if (in_array($parent, class_parents($class))) {
                    $newInstance = Reflect::create($class, $data);
                    context()->bind($class, $newInstance);

                    return $newInstance;
                }
            }
        }

        if (interface_exists($class)) {
            foreach (static::$singletones as $s) {
                if (object_implements($s, $class)) {
                    $newInstance = Reflect::create($s, $data);
                    context()->bind($class, $newInstance);

                    return $newInstance;
                }
            }
        }
    }
}

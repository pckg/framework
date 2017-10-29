<?php namespace Pckg\Framework\Reflect;

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
use Pckg\Generic\Service\Generic;
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
        Generic::class,
    ];

    protected static $parents = [
        Provider::class,
    ];

    protected static $bind = [
        Router::class       => 'Router',
        Context::class      => 'Context',
        Config::class       => 'Config',
        AssetManager::class => 'AssetManager',
        MetaManager::class  => 'MetaManager',
        SeoManager::class   => 'SeoManager',
        Flash::class        => 'Flash',
        Response::class     => 'Response',
        Request::class      => 'Request',
        Lang::class         => 'Lang',
        Auth::class         => 'Auth',
        Locale::class       => 'Locale',
        Generic::class      => 'Generic',
    ];

    public function canResolve($class)
    {
        return isset(static::$bind[$class]) || in_array($class, static::$singletones);
    }

    public function resolve($class)
    {
        if (isset(static::$bind[$class])) {
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
                $newInstance = Reflect::create($class);

                if (isset(static::$bind[$class])) {
                    context()->bind($class, $newInstance);

                    return $newInstance;
                }
            }

            foreach (static::$parents as $parent) {
                if (in_array($parent, class_parents($class))) {
                    $newInstance = Reflect::create($class);
                    context()->bind($class, $newInstance);

                    return $newInstance;
                }
            }
        }

        if (interface_exists($class)) {
            foreach (static::$singletones as $s) {
                if (object_implements($s, $class)) {
                    $newInstance = Reflect::create($s);
                    context()->bind($class, $newInstance);

                    return $newInstance;
                }
            }
        }
    }
}

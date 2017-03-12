<?php namespace Pckg\Framework\Reflect;

use Pckg\Concept\Context;
use Pckg\Concept\Reflect;
use Pckg\Concept\Reflect\Resolver;
use Pckg\Framework\Config;
use Pckg\Framework\Locale\Lang;
use Pckg\Framework\Request;
use Pckg\Framework\Request\Data\Flash;
use Pckg\Framework\Response;
use Pckg\Framework\Router;
use Pckg\Generic\Service\Generic;
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
        Locale::class,
        Generic::class,
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
        Locale::class       => 'Locale',
        Generic::class      => 'Generic',
    ];

    public function resolve($class)
    {
        if (isset(static::$bind[$class]) && context()->exists($class)) {
            return context()->get($class);
        }

        if (class_exists($class) && in_array($class, static::$singletones)) {
            $newInstance = Reflect::create($class);

            if (isset(static::$bind[$class])) {
                context()->bind($class, $newInstance);

                return $newInstance;
            }
        }

        foreach (context()->getData() as $object) {
            if (is_object($object)) {
                if (get_class($object) === $class || is_subclass_of($object, $class)) {
                    return $object;
                } else if (in_array($class, class_implements($object))) {
                    return $object;
                }
            }
        }
    }
}

<?php namespace Pckg\Framework\Reflect;

use Pckg\Concept\Context;
use Pckg\Concept\Reflect;
use Pckg\Concept\Reflect\Resolver;
use Pckg\Framework\AssetManager;
use Pckg\Framework\Config;
use Pckg\Framework\Router;

class FrameworkResolver implements Resolver
{

    protected static $singletones = [
        Router::class,
        Context::class,
        Config::class,
        AssetManager::class,
    ];

    protected static $bind = [
        Router::class       => 'Router',
        Context::class      => 'Context',
        Config::class       => 'Config',
        AssetManager::class => 'AssetManager',
    ];

    public function resolve($class)
    {
        if (isset(static::$bind[$class]) && context()->exists(static::$bind[$class])) {
            return context()->get(static::$bind[$class]);
        }

        if (class_exists($class) && in_array($class, static::$singletones)) {
            $newInstance = Reflect::create($class);

            if (isset(static::$bind[$class])) {
                context()->bind(static::$bind[$class], $newInstance);
            }

            return $newInstance;
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
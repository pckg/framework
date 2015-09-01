<?php

namespace Pckg\Framework\Helper;

use Pckg\Concept\Context;
use Pckg\Framework\Router;

class Reflect extends \Pckg\Concept\Reflect
{

    protected static $singletones = [
        Router::class,
        Context::class,
    ];

    protected static function createHintedParameter($class, $data) {
        if (class_exists($class)) {
            $newInstance = static::create($class, $data);

            if (in_array($class, static::$singletones)) {
                context()->bind($class, $newInstance);
            }

            return $newInstance;
        }
    }

    protected static function getData($data) {
        return [$data, context()->getData()];
    }

}
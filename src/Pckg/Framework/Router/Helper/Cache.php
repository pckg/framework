<?php namespace Pckg\Framework\Router\Helper;

use Pckg\Cache\Cache as PckgCache;

class Cache extends PckgCache
{

    protected function getCachePath()
    {
        return path('cache') . 'framework/router_' . str_replace(
            ['\\', '/'],
            '_',
            (get_class(app()) . '_' . get_class(env()))
        ) . '.cache';
    }

}
<?php

namespace Pckg\Framework\Router\Helper;

use Pckg\Framework\Cache as FrameworkCache;

class Cache extends FrameworkCache {

    protected function getCachePath() {
        return path('cache') . 'framework/router_' . str_replace(['\\', '/'], '_', (get_class(app()) . '_' . get_class(env()))) . '.cache';
    }

}
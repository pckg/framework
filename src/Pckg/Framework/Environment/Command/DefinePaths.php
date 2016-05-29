<?php

namespace Pckg\Framework\Environment\Command;

use Pckg\Concept\AbstractChainOfReponsibility;


class DefinePaths extends AbstractChainOfReponsibility
{

    public function execute(callable $next)
    {
        path('ds', DIRECTORY_SEPARATOR);
        path('root', realpath(isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'] ? $_SERVER['DOCUMENT_ROOT'] : BASE_PATH) . path('ds'));

        path("apps", path('root') . "app" . path('ds'));
        path("cache", path('root') . "cache" . path('ds'));
        path("storage", path('root') . "storage" . path('ds'));
        path("www", path('root') . "www" . path('ds'));
        path("tmp", path('cache') . "tmp" . path('ds'));
        path("uploads", path('www') . "uploads" . path('ds'));
        path("vendor", path('root') . "vendor" . path('ds'));

        return $next();
    }

}

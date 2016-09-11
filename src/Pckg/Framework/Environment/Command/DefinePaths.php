<?php namespace Pckg\Framework\Environment\Command;

use Pckg\Concept\AbstractChainOfReponsibility;

class DefinePaths extends AbstractChainOfReponsibility
{

    public function execute(callable $next)
    {
        $root = isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'] ? $_SERVER['DOCUMENT_ROOT'] : BASE_PATH;

        path('ds', substr(BASE_PATH, 0, 1) == '/' ? '/' : '\\');
        path('root', realpath($root) . path('ds'));

        path("apps", path('root') . "app" . path('ds'));
        path("storage", path('root') . "storage" . path('ds'));
        path("www", path('root') . "www" . path('ds'));
        path("cache", path('storage') . "cache" . path('ds'));
        path("tmp", path('storage') . "tmp" . path('ds'));
        path("uploads", path('www') . "uploads" . path('ds'));
        path("vendor", path('root') . "vendor" . path('ds'));

        return $next();
    }

}

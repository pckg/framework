<?php

namespace Pckg\Environment\Command;

use Pckg\Concept\AbstractChainOfReponsibility;


class DefinePaths extends AbstractChainOfReponsibility
{

    public function execute()
    {
        path('ds', DIRECTORY_SEPARATOR);
        path('root', $_SERVER['DOCUMENT_ROOT']);

        path("lib", path('root') . "lib" . path('ds'));
        path("log", path('root') . "logs" . path('ds'));
        path("apps", path('root') . "app" . path('ds'));
        path("src", path('root') . "src" . path('ds'));
        path("cache", path('root') . "cache" . path('ds'));
        path("www", path('root') . "www" . path('ds'));
        path("core", path('src') . "core" . path('ds'));
        path("tmp", path('cache') . "tmp" . path('ds'));
        path("uploads", path('www') . "uploads" . path('ds'));
        path("vendor", path('root') . "vendor" . path('ds'));

        $this->next->execute();
    }

}
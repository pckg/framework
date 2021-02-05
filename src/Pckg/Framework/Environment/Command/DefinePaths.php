<?php

namespace Pckg\Framework\Environment\Command;

use Pckg\Concept\AbstractChainOfReponsibility;

class DefinePaths extends AbstractChainOfReponsibility
{

    public function execute(callable $next)
    {
        path('ds', substr(BASE_PATH, 0, 1) == '/' ? '/' : '\\');
        path('root', defined('__ROOT__') ? __ROOT__ : BASE_PATH);

        path('apps', path('root') . 'app' . path('ds'));
        path('src', path('root') . 'src' . path('ds'));
        path('storage', path('root') . 'storage' . path('ds'));
        path('private', path('root') . 'storage' . path('ds') . 'private' . path('ds'));
        path('public', path('root') . 'storage' . path('ds') . 'public' . path('ds'));
        path('www', path('root') . 'www' . path('ds'));
        path('cache', path('storage') . 'cache' . path('ds'));
        path('tmp', path('storage') . 'tmp' . path('ds'));
        path('uploads', path('storage') . 'uploads' . path('ds'));
        path('vendor', path('root') . 'vendor' . path('ds'));
        path('build', path('root') . 'build' . path('ds'));

        return $next();
    }
}

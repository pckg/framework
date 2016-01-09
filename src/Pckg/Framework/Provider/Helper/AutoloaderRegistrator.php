<?php

namespace Pckg\Framework\Provider\Helper;

use Pckg\Concept\Reflect;
use Pckg\Framework\View\Twig;

trait AutoloaderRegistrator
{

    public function registerAutoloaders($autoloaders, $afterAutoload = true)
    {
        foreach ($autoloaders as $autoloader) {
            autoloader()->add('', $autoloader);
            Twig::addDir($autoloader);
        }

        if ($afterAutoload === true) {
            $afterAutoload = $this;
        }

        if (is_object($afterAutoload) && method_exists($afterAutoload, 'afterAutoload')) {
            Reflect::method($afterAutoload, 'afterAutoload');
        }
    }

}
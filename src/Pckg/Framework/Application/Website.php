<?php

namespace Pckg\Framework\Application;

use Pckg\Framework\Application;
use Pckg\Framework\Asset\AssetManager;
use Pckg\Framework\Router\RouterManager;

class Website extends Application implements AssetManager, RouterManager
{

    public function assets()
    {
        return [];
    }

    public function routes()
    {
        return [];
    }

}
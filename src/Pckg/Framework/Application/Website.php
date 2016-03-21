<?php

namespace Pckg\Framework\Application;

use Pckg\Framework\Application;
use Pckg\Framework\Router\RouterManager;
use Pckg\Manager\Asset\AssetManager;

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
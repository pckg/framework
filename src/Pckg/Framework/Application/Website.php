<?php

namespace Pckg\Framework\Application;

use Pckg\Framework\Application;
use Pckg\Framework\Router\RouterManager;
use Pckg\Manager\Asset\AssetManager;

class Website extends Application implements AssetManager
{

    public function assets()
    {
        return [];
    }

}
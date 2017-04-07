<?php

namespace Pckg\Framework\Application\Website\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Framework\Environment;
use Pckg\Manager\Asset as AssetManager;

class InitLastAssets extends AbstractChainOfReponsibility
{

    protected $assetManager;

    protected $environment;

    public function __construct(
        Environment $environment,
        AssetManager $assetManager
    ) {
        $this->assetManager = $assetManager;
        $this->environment = $environment;
    }

    public function execute(callable $next)
    {
        foreach ($this->environment->assets() as $asset) {
            $this->assetManager->addAssets($asset, 'footer');
        }

        return $next();
    }

}
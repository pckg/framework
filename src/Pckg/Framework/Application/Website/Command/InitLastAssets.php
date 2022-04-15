<?php

namespace Pckg\Framework\Application\Website\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Framework\Environment;
use Pckg\Manager\Asset as AssetManager;

/**
 * Class InitLastAssets
 * @package Pckg\Framework\Application\Website\Command
 * @deprecated
 */
class InitLastAssets extends AbstractChainOfReponsibility
{
    protected $assetManager;

    protected $environment;

    public function __construct(Environment $environment, AssetManager $assetManager)
    {
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

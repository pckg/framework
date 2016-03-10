<?php

namespace Pckg\Framework\Application\Website\Command;

use Pckg\Framework\Application\ApplicationInterface;
use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Framework\AssetManager;
use Pckg\Framework\View\Twig;

class InitAssets extends AbstractChainOfReponsibility
{

    protected $website;

    protected $assetManager;

    public function __construct(ApplicationInterface $website, AssetManager $assetManager)
    {
        $this->website = $website;
        $this->assetManager = $assetManager;
    }

    public function execute(callable $next)
    {
        Twig::addStaticData('_assetManager', $this->assetManager);

        foreach ($this->website->assets() as $asset) {
            $this->assetManager->addAssets('app/' . strtolower(get_class($this->website)) . '/public/' . $asset);
        }

        return $next();
    }


}
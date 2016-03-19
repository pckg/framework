<?php

namespace Pckg\Framework\Application\Website\Command;

use Pckg\Framework\Application\ApplicationInterface;
use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Framework\View\Twig;
use Pckg\Manager\Meta as MetaManager;
use Pckg\Manager\Asset as AssetManager;

class InitAssets extends AbstractChainOfReponsibility
{

    protected $website;

    protected $assetManager;

    protected $metaManager;

    public function __construct(ApplicationInterface $website, AssetManager $assetManager, MetaManager $metaManager)
    {
        $this->website = $website;
        $this->assetManager = $assetManager;
        $this->metaManager = $metaManager;
    }

    public function execute(callable $next)
    {
        Twig::addStaticData('_assetManager', $this->assetManager);
        Twig::addStaticData('_metaManager', $this->metaManager);

        foreach ($this->website->assets() as $asset) {
            $this->assetManager->addAssets('app/' . strtolower(get_class($this->website)) . '/public/' . $asset);
        }

        return $next();
    }


}
<?php

namespace Pckg\Framework\Application\Website\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Framework\Application\ApplicationInterface;
use Pckg\Framework\View\Twig;
use Pckg\Manager\Asset as AssetManager;
use Pckg\Manager\Meta as MetaManager;
use Pckg\Manager\Seo as SeoManager;

class InitAssets extends AbstractChainOfReponsibility
{

    protected $website;

    protected $assetManager;

    protected $metaManager;

    protected $seoManager;

    public function __construct(ApplicationInterface $website, AssetManager $assetManager, MetaManager $metaManager, SeoManager $seoManager)
    {
        $this->website = $website;
        $this->assetManager = $assetManager;
        $this->metaManager = $metaManager;
        $this->seoManager = $seoManager;
    }

    public function execute(callable $next)
    {
        Twig::addStaticData('_assetManager', $this->assetManager);
        Twig::addStaticData('_metaManager', $this->metaManager);
        Twig::addStaticData('_seoManager', $this->seoManager);

        foreach ($this->website->assets() as $asset) {
            $this->assetManager->addAssets('app/' . strtolower(get_class($this->website)) . '/public/' . $asset);
        }

        return $next();
    }


}
<?php

namespace Pckg\Framework\Application\Website\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Framework\Environment;
use Pckg\Framework\Helper\Context;
use Pckg\Framework\Request\Data\Flash;
use Pckg\Framework\View\Twig;
use Pckg\Manager\Asset as AssetManager;
use Pckg\Manager\Meta as MetaManager;
use Pckg\Manager\Seo as SeoManager;

class InitAssets extends AbstractChainOfReponsibility
{

    protected $application;

    protected $assetManager;

    protected $metaManager;

    protected $seoManager;

    protected $environment;

    protected $flash;

    public function __construct(
        Context $context,
        Environment $environment,
        AssetManager $assetManager,
        MetaManager $metaManager,
        SeoManager $seoManager,
        Flash $flash
    ) {
        $this->assetManager = $assetManager;
        $this->metaManager = $metaManager;
        $this->seoManager = $seoManager;
        $this->environment = $environment;
        $this->flash = $flash;
    }

    public function execute(callable $next)
    {
        Twig::addStaticData('_assetManager', $this->assetManager);
        Twig::addStaticData('_metaManager', $this->metaManager);
        Twig::addStaticData('_seoManager', $this->seoManager);
        Twig::addStaticData('_flash', $this->flash);

        foreach ($this->environment->assets() as $asset) {
            $this->assetManager->addAssets($asset, 'footer');
        }

        return $next();
    }


}
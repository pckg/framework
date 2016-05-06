<?php

namespace Pckg\Framework\Application\Website\Command;

use Pckg\Auth\Service\Auth;
use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Concept\Context;
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

    protected $flash;

    protected $auth;

    public function __construct(
        Context $context,
        AssetManager $assetManager,
        MetaManager $metaManager,
        SeoManager $seoManager,
        Flash $flash,
        Auth $auth
    ) {
        $this->assetManager = $assetManager;
        $this->metaManager = $metaManager;
        $this->seoManager = $seoManager;
        $this->flash = $flash;
        $this->auth = $auth;
    }

    public function execute(callable $next)
    {
        Twig::addStaticData('_assetManager', $this->assetManager);
        Twig::addStaticData('_metaManager', $this->metaManager);
        Twig::addStaticData('_seoManager', $this->seoManager);
        Twig::addStaticData('_flash', $this->flash);
        Twig::addStaticData('_auth', $this->auth);

        return $next();
    }


}
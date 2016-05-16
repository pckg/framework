<?php

namespace Pckg\Framework\Application\Website\Command;

use Pckg\Auth\Service\Auth;
use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Framework\Request\Data\Flash;
use Pckg\Framework\View\Twig;
use Pckg\Generic\Service\Menu;
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

    protected $menuService;

    public function __construct(
        AssetManager $assetManager,
        MetaManager $metaManager,
        SeoManager $seoManager,
        Flash $flash,
        Auth $auth,
        Menu $menuService
    ) {
        $this->assetManager = $assetManager;
        $this->metaManager = $metaManager;
        $this->seoManager = $seoManager;
        $this->flash = $flash;
        $this->auth = $auth;
        $this->menuService = $menuService;
    }

    public function execute(callable $next)
    {
        Twig::addStaticData('_assetManager', $this->assetManager);
        Twig::addStaticData('_metaManager', $this->metaManager);
        Twig::addStaticData('_seoManager', $this->seoManager);
        Twig::addStaticData('_flash', $this->flash);
        Twig::addStaticData('_auth', $this->auth);
        Twig::addStaticData('_menuService', $this->menuService);

        return $next();
    }


}
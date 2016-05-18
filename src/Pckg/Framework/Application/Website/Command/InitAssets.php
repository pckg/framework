<?php

namespace Pckg\Framework\Application\Website\Command;

use Pckg\Auth\Service\Auth;
use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Framework\Request\Data\Flash;
use Pckg\Framework\Service\Plugin;
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

    protected $pluginService;

    public function __construct(
        AssetManager $assetManager,
        MetaManager $metaManager,
        SeoManager $seoManager,
        Flash $flash,
        Auth $auth,
        Menu $menuService,
        Plugin $pluginService
    ) {
        /**
         * @T00D00 - move this to asset provider
         */
        $this->assetManager = $assetManager;
        $this->metaManager = $metaManager;
        $this->seoManager = $seoManager;
        /**
         * @T00D00 - move this to framework provider
         */
        $this->flash = $flash;
        /**
         * @T00D00 - move this to auth provider
         */
        $this->auth = $auth;
        /**
         * @T00D00 - move this to generic provider
         */
        $this->menuService = $menuService;
        /**
         * @T00D00 - move this to framework provider
         */
        $this->pluginService = $pluginService;
    }

    public function execute(callable $next)
    {
        Twig::addStaticData('_assetManager', $this->assetManager);
        Twig::addStaticData('_metaManager', $this->metaManager);
        Twig::addStaticData('_seoManager', $this->seoManager);
        Twig::addStaticData('_flash', $this->flash);
        Twig::addStaticData('_auth', $this->auth);
        Twig::addStaticData('_menuService', $this->menuService);
        Twig::addStaticData('_debugBar', debugBar());
        Twig::addStaticData('_pluginService', $this->pluginService);

        return $next();
    }


}
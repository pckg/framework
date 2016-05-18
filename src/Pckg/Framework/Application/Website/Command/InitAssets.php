<?php

namespace Pckg\Framework\Application\Website\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Framework\Request\Data\Flash;
use Pckg\Framework\View\Twig;
use Pckg\Manager\Asset as AssetManager;

class InitAssets extends AbstractChainOfReponsibility
{

    protected $flash;

    public function __construct(
        Flash $flash
    ) {
        /**
         * @T00D00 - move this to framework provider
         */
        $this->flash = $flash;
    }

    public function execute(callable $next)
    {
        Twig::addStaticData('_flash', $this->flash);
        Twig::addStaticData('_debugBar', debugBar());

        return $next();
    }


}
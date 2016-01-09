<?php

namespace Pckg\Framework\Router\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Framework\Application\Website;
use Pckg\Framework\Router\Helper\RouteRegistrator;

class RegisterRoutes extends AbstractChainOfReponsibility
{

    use RouteRegistrator;

    protected $website;

    public function __construct(Website $website)
    {
        $this->website = $website;
    }

    public function execute(callable $next)
    {
        $this->registerRoutes($this->website->routes());

        return $next();
    }

}
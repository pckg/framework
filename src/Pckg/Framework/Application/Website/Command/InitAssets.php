<?php

namespace Pckg\Framework\Application\Website\Command;

use Pckg\Framework\Application\ApplicationInterface;
use Pckg\Concept\AbstractChainOfReponsibility;

use Pckg\Framework\Helper\Optimize;

class InitAssets extends AbstractChainOfReponsibility
{

    protected $website;

    public function __construct(ApplicationInterface $website)
    {
        $this->website = $website;
    }

    public function execute(callable $next)
    {
        foreach ($this->website->assets() as $asset) {
            $expl = explode('/', $asset);
            Optimize::addFile($expl[0], 'app/' . '' . '/public/' . $asset);
        }

        return $next();
    }


}
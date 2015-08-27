<?php

namespace Pckg\Application\Website\Command;

use Pckg\Application\ApplicationInterface;
use Pckg\Concept\AbstractChainOfReponsibility;

use Pckg\Helper\Optimize;

class InitAssets extends AbstractChainOfReponsibility
{

    protected $website;

    public function __construct(ApplicationInterface $website)
    {
        $this->website = $website;
    }

    public function execute()
    {
        foreach ($this->website->assets() as $asset) {
            $expl = explode('/', $asset);
            Optimize::addFile($expl[0], 'app/' . $this->website->getName() . '/public/' . $asset);
        }

        $this->next->execute();
    }


}
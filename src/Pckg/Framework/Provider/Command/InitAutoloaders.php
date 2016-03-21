<?php

namespace Pckg\Framework\Provider\Command;

use Pckg\Concept\AbstractChainOfReponsibility;
use Pckg\Framework\Application;
use Pckg\Framework\Provider\AutoloaderManager;
use Pckg\Framework\Provider\Helper\Registrator;

class InitAutoloaders extends AbstractChainOfReponsibility
{

    use Registrator;

    protected $manager;

    public function __construct(AutoloaderManager $manager)
    {
        $this->manager = $manager;
    }

    public function execute(callable $next)
    {
        $this->registerAutoloaders($this->manager->autoload(), $this->manager);

        return $next();
    }

}